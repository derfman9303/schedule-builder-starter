<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_department(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/departments', [
            'name' => 'Sales Department',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'name',
            'user_id',
            'created_at',
            'updated_at',
        ]);

        $this->assertDatabaseHas('departments', [
            'name' => 'Sales Department',
            'user_id' => $user->id,
        ]);
    }

    public function test_department_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/departments', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_user_can_only_see_their_own_departments(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Department::factory()->create(['user_id' => $user1->id, 'name' => 'User 1 Dept']);
        Department::factory()->create(['user_id' => $user2->id, 'name' => 'User 2 Dept']);

        $response = $this->actingAs($user1)->getJson('/api/departments');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['name' => 'User 1 Dept']);
        $response->assertJsonMissing(['name' => 'User 2 Dept']);
    }

    public function test_user_can_delete_their_own_department(): void
    {
        $user = User::factory()->create();
        $department = Department::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/departments/{$department->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    public function test_user_cannot_delete_other_users_department(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $department = Department::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->deleteJson("/api/departments/{$department->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('departments', ['id' => $department->id]);
    }

    public function test_user_can_view_their_own_department(): void
    {
        $user = User::factory()->create();
        $department = Department::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/departments/{$department->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => $department->name]);
    }

    public function test_user_cannot_view_other_users_department(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $department = Department::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->getJson("/api/departments/{$department->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_update_their_own_department(): void
    {
        $user = User::factory()->create();
        $department = Department::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson("/api/departments/{$department->id}", [
            'name' => 'Updated Department Name',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'name' => 'Updated Department Name',
        ]);
    }

    public function test_user_cannot_update_other_users_department(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $department = Department::factory()->create(['user_id' => $user2->id, 'name' => 'Original Name']);

        $response = $this->actingAs($user1)->putJson("/api/departments/{$department->id}", [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'name' => 'Original Name',
        ]);
    }
}
