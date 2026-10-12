<?php

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\Media;
use App\Models\Project;
use App\Models\User;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Database\Seeders\RolesAndPermissionsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('refuses panel access to a user without roles or permissions', function (): void {
    $this->actingAs(User::factory()->create());

    $this->get('/admin')->assertForbidden();
});

it('allows panel users to see the dashboard but refuses resource URLs', function (string $path): void {
    $this->seed(RolesAndPermissionsSeeder::class);
    $user = User::factory()->create()->assignRole('panel_user');
    $this->actingAs($user);

    $this->get('/admin')->assertOk()->assertDontSee('إضافة مشروع جديد');
    $this->get('/admin/'.$path)->assertForbidden();
})->with([
    'departments', 'project-categories', 'project-types', 'design-styles',
    'question-sections', 'answer-types', 'questions', 'question-options',
    'question-contexts', 'question-conditions', 'clients', 'projects',
    'answers', 'answer-options', 'media', 'users', 'shield/roles',
]);

it('enforces every generated resource permission through its policy', function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
    $user = User::factory()->create()->assignRole('panel_user');

    foreach (FilamentShield::getResources() as $resource) {
        $model = $resource['modelFqcn'];
        $record = new $model;
        foreach (['viewAny', 'view', 'create', 'update', 'delete'] as $ability) {
            $arguments = in_array($ability, ['view', 'update', 'delete']) ? $record : $model;
            $permission = ucfirst($ability).':'.class_basename($model);

            expect(Gate::forUser($user)->allows($ability, $arguments))->toBeFalse();
            $user->givePermissionTo($permission);
            expect(Gate::forUser($user)->allows($ability, $arguments))
                ->toBe(! ($model === Media::class && $ability === 'create'));
            $user->revokePermissionTo($permission);
        }
    }
});

it('permits a custom read only role without granting create or edit access', function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
    $role = Role::create(['name' => 'قارئ المشاريع', 'guard_name' => 'web']);
    $role->givePermissionTo(['View:Dashboard', 'ViewAny:Project', 'View:Project']);
    $user = User::factory()->create()->assignRole($role);
    $this->actingAs($user);
    $project = Project::factory()->create();

    $this->get('/admin/projects')->assertOk();
    $this->get('/admin/projects/'.$project->id)->assertOk();
    $this->get('/admin/projects/create')->assertForbidden();
    $this->get('/admin/projects/'.$project->id.'/edit')->assertForbidden();
    $this->get('/admin/users')->assertForbidden();
});

it('protects the dashboard independently and shows the project shortcut only with create permission', function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
    $role = Role::create(['name' => 'محرر المشاريع', 'guard_name' => 'web']);
    $role->givePermissionTo('Create:Project');
    $user = User::factory()->create()->assignRole($role);
    $this->actingAs($user);

    $this->get('/admin')->assertForbidden();
    $role->givePermissionTo('View:Dashboard');
    $user->unsetRelation('roles');
    $this->get('/admin')->assertOk()->assertSee('إضافة مشروع جديد');
});

it('creates and updates a role with selected permissions through Shield', function (): void {
    $this->actingAs(User::factory()->administrator()->create());

    Livewire::test(CreateRole::class)
        ->fillForm(['name' => 'مراجع المشاريع', 'guard_name' => 'web', ProjectResource::class => ['ViewAny:Project'], 'pages_tab' => ['View:Dashboard']])
        ->call('create')->assertHasNoFormErrors();

    $role = Role::where('name', 'مراجع المشاريع')->sole();
    expect($role->hasPermissionTo('ViewAny:Project'))->toBeTrue();
    expect($role->hasPermissionTo('Create:Project'))->toBeFalse();

    Livewire::test(EditRole::class, ['record' => $role->id])
        ->fillForm([ProjectResource::class => ['ViewAny:Project', 'Create:Project']])
        ->call('save')->assertHasNoFormErrors();

    expect($role->fresh()->hasPermissionTo('Create:Project'))->toBeTrue();

    Livewire::test(EditRole::class, ['record' => $role->id])
        ->fillForm([ProjectResource::class => [], 'pages_tab' => []])
        ->call('save')->assertHasNoFormErrors();

    expect($role->fresh()->permissions)->toBeEmpty();
});

it('renders the dedicated roles list create form and role details in Arabic', function (): void {
    $admin = User::factory()->administrator()->create();
    $this->actingAs($admin);
    $role = Role::findByName('super_admin');

    $this->get('/admin')->assertOk()->assertDontSee('إدارة الأدوار والصلاحيات');
    $this->get('/admin/shield/roles')->assertOk()
        ->assertSee('إضافة دور جديد')->assertSee('عدد المستخدمين')->assertSee('عدد الصلاحيات')
        ->assertSee('إسناد الأدوار للمستخدمين');
    $roleNavigationItems = collect(Filament::getNavigation())
        ->flatMap(fn ($group): iterable => $group->getItems())
        ->filter(fn ($item): bool => $item->getLabel() === 'الأدوار');
    expect($roleNavigationItems)->toHaveCount(1);
    $this->get('/admin/shield/roles/create')->assertOk()
        ->assertSee('اسم الدور')->assertSee('العمليات المسموحة')->assertSee('المشاريع')
        ->assertSee('فتح الصفحة وعرض قائمة السجلات.');
    $this->get('/admin/shield/roles/'.$role->id)->assertOk()
        ->assertSee('ملخص الدور')->assertSee($admin->name)->assertSee('الصلاحيات الممنوحة');
});

it('creates roles for the panel guard and grants dashboard access by default', function (): void {
    $this->actingAs(User::factory()->administrator()->create());

    Livewire::test(CreateRole::class)
        ->fillForm(['name' => 'موظف جديد', 'guard_name' => 'invalid'])
        ->call('create')->assertHasNoFormErrors();

    $role = Role::where('name', 'موظف جديد')->sole();
    expect($role->guard_name)->toBe('web');
    expect($role->hasPermissionTo('View:Dashboard'))->toBeTrue();
    expect($role->hasPermissionTo('Create:Project'))->toBeFalse();

    Livewire::test(EditRole::class, ['record' => $role->id])
        ->fillForm(['guard_name' => 'invalid'])
        ->call('save')->assertHasNoFormErrors();

    expect($role->fresh()->guard_name)->toBe('web');
    expect($role->fresh()->hasPermissionTo('View:Dashboard'))->toBeTrue();
});

it('grants all page and resource permissions with the select all control', function (): void {
    $this->actingAs(User::factory()->administrator()->create());

    Livewire::test(CreateRole::class)
        ->fillForm(['name' => 'مسؤول الإدارة'])
        ->set('data.select_all', true)
        ->call('create')->assertHasNoFormErrors();

    $role = Role::where('name', 'مسؤول الإدارة')->sole();
    expect($role->permissions->pluck('name')->sort()->values()->all())
        ->toBe(Permission::where('guard_name', 'web')->pluck('name')->sort()->values()->all());
});

it('creates users with roles and direct permissions and updates their assignments', function (): void {
    $this->actingAs(User::factory()->administrator()->create());
    $role = Role::findByName('panel_user');
    $permission = Permission::findByName('ViewAny:Project');

    Livewire::test(CreateUser::class)
        ->fillForm(['name' => 'مراجع', 'email' => 'reviewer@example.test', 'password' => 'secure-reviewer-password', 'roles' => [$role->id], 'permissions' => [$permission->id]])
        ->call('create')->assertHasNoFormErrors();

    $user = User::where('email', 'reviewer@example.test')->sole();
    expect($user->hasRole('panel_user'))->toBeTrue();
    expect($user->hasDirectPermission('ViewAny:Project'))->toBeTrue();

    Livewire::test(EditUser::class, ['record' => $user->id])
        ->fillForm(['roles' => [], 'permissions' => []])
        ->call('save')->assertHasNoFormErrors();

    expect($user->fresh()->canAccessPanel(Filament::getPanel('admin')))->toBeFalse();
});

it('prevents a user editor from assigning administrative roles or direct permissions', function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
    $editor = User::factory()->create()->assignRole('panel_user');
    $editor->givePermissionTo(['ViewAny:User', 'View:User', 'Create:User', 'Update:User']);
    $this->actingAs($editor);
    $target = User::factory()->create()->assignRole('panel_user');
    $adminRole = Role::findByName('super_admin');
    $permission = Permission::findByName('Update:Role');

    Livewire::test(EditUser::class, ['record' => $target->id])
        ->fillForm(['name' => 'اسم معدل', 'roles' => [$adminRole->id], 'permissions' => [$permission->id]])
        ->call('save')->assertHasNoFormErrors();

    expect($target->fresh()->name)->toBe('اسم معدل');
    expect($target->fresh()->hasRole('super_admin'))->toBeFalse();
    expect($target->fresh()->hasDirectPermission('Update:Role'))->toBeFalse();
});
