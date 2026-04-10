<?php

use App\Policies\UserPolicy;
use App\Models\User;

it('allows super admin to assign a role', function () {
    $user = mock(User::class)->makePartial();//avecc mock, on cree un faux objet User sans bd, Eloquent, sans rien du framework
    //makePartial permet de laisser certaine method de la class fonctionner normalement permettant les $user->is ou $user->can ...

    $user->shouldReceive('hasRole')->with('super-admin')->andReturn(true);//$user = true si la policy appelle $user->hasRole('super-admin')

    $target = mock(User::class)->makePartial();

    $policy = new UserPolicy();

    expect($policy->assignRole($user, $target))->toBeTrue();
    expect($policy->create($user))->toBeTrue();
});

it('denies admin to assign a role', function () {
    $user = mock(User::class)->makePartial();
    $user->shouldReceive('hasRole')->with('super-admin')->andReturn(false);

    $target = mock(User::class)->makePartial();

    $policy = new UserPolicy();

    expect($policy->assignRole($user, $target))->toBeFalse();
    //expect($policy->viewAny($user))->toBeFalse(); n'est plus un test unitaire car viewAny utilise Gate qui depend du framework donc instanciation,etc
});



/* Des qu'une policy appelle appelle Gate , $user->can(), $user->is() et une permission Spatie,
 une façade,un helper Laravel ce n'zest plus un test unitaire mais un test d'integration */