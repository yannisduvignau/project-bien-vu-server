<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Requests\GenererRequest;
use Illuminate\Support\Facades\Validator;

class GenererRequestTest extends TestCase
{

    // --------------------------------------------------------------
    //                  Good validation
    // --------------------------------------------------------------
    public function test_generer_request_with_valid_data_passes_validation()
    {
        $data = [
            'type' => 'Appartement',
            'surface' => 100,
            'pieces' => 4,
            'ville' => 'Bordeaux'
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->fails(), "La validation devrait passer avec des données valides.");
    }

    public function test_generer_request_with_valid_data_passes_validation_with_other_ignored_params()
    {
        $data = [
            'type' => 'Appartement',
            'surface' => 100,
            'pieces' => 4,
            'ville' => 'Bordeaux',
            'location' => true
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->fails(), "La validation devrait passer avec des données valides.");
    }







    // --------------------------------------------------------------
    //                  0 Good validation
    // --------------------------------------------------------------
    public function test_generer_request_with_invalid_data_passes_validation()
    {
        $data = [];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer sans le type.");
        $this->assertArrayHasKey('type', $validator->errors()->toArray());
        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la surface.");
        $this->assertArrayHasKey('surface', $validator->errors()->toArray());
        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la piece.");
        $this->assertArrayHasKey('pieces', $validator->errors()->toArray());
        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la ville.");
        $this->assertArrayHasKey('ville', $validator->errors()->toArray());
    }










    // --------------------------------------------------------------
    //                  param Type failed
    // --------------------------------------------------------------
    public function test_generer_request_without_type_fails_validation()
    {
        $data = [
            'surface' => 100,
            'pieces' => 4,
            'ville' => 'Bordeaux'
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer sans le type.");
        $this->assertArrayHasKey('type', $validator->errors()->toArray());
    }

    public function test_generer_request_with_non_string_type_fails_validation()
    {
        $data = [
            'type' => 12345, // Type incorrect (doit être une string)
            'surface' => 100,
            'pieces' => 4,
            'ville' => 'Bordeaux'
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer si le type n'est pas une chaîne.");
        $this->assertArrayHasKey('type', $validator->errors()->toArray());
    }

    public function test_generer_request_with_max_type_fails_validation()
    {
        $data = [
            'type' => 'azertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiop',
            'surface' => 100,
            'pieces' => 4,
            'ville' => 'Bordeaux'
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer si le type n'est pas une chaîne.");
        $this->assertArrayHasKey('type', $validator->errors()->toArray());
    }







    // --------------------------------------------------------------
    //                  param Surface failed
    // --------------------------------------------------------------
    public function test_generer_request_without_surface_fails_validation()
    {
        $data = [
            'type' => 'Appartement',
            'pieces' => 4,
            'ville' => 'Bordeaux'
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la surface.");
        $this->assertArrayHasKey('surface', $validator->errors()->toArray());
    }

    public function test_generer_request_with_non_numeric_surface_fails_validation()
    {
        $data = [
            'type' => 'Appartement',
            'surface' => 'abc',
            'pieces' => 4,
            'ville' => 'Bordeaux'
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer si la surface n'est pas un nombre.");
        $this->assertArrayHasKey('surface', $validator->errors()->toArray());
    }

    public function test_generer_request_with_min_surface_fails_validation()
    {
        $data = [
            'type' => 'Appartement',
            'surface' => 0,
            'pieces' => 4,
            'ville' => 'Bordeaux'
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer si la surface n'est pas un nombre.");
        $this->assertArrayHasKey('surface', $validator->errors()->toArray());
    }







    // --------------------------------------------------------------
    //                  param Pieces failed
    // --------------------------------------------------------------
    public function test_generer_request_without_pieces_fails_validation()
    {
        $data = [
            'type' => 'Appartement',
            'surface' => 100,
            'ville' => 'Bordeaux'
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer sans le nombre de pièces.");
        $this->assertArrayHasKey('pieces', $validator->errors()->toArray());
    }

    public function test_generer_request_with_non_integer_pieces_fails_validation()
    {
        $data = [
            'type' => 'Appartement',
            'surface' => 100,
            'pieces' => 'quatre', // Erreur : Doit être un entier
            'ville' => 'Bordeaux'
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer si le nombre de pièces n'est pas un entier.");
        $this->assertArrayHasKey('pieces', $validator->errors()->toArray());
    }

    public function test_generer_request_with_min_pieces_fails_validation()
    {
        $data = [
            'type' => 'Appartement',
            'surface' => 100,
            'pieces' => 0, // Erreur : Doit être un entier
            'ville' => 'Bordeaux'
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer si le nombre de pièces n'est pas un entier.");
        $this->assertArrayHasKey('pieces', $validator->errors()->toArray());
    }







    // --------------------------------------------------------------
    //                  param Ville failed
    // --------------------------------------------------------------
    public function test_generer_request_without_ville_fails_validation()
    {
        $data = [
            'type' => 'Appartement',
            'surface' => 100,
            'pieces' => 4
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la ville.");
        $this->assertArrayHasKey('ville', $validator->errors()->toArray());
    }

    public function test_generer_request_with_non_string_ville_fails_validation()
    {
        $data = [
            'type' => 'Appartement',
            'surface' => 100,
            'pieces' => 4,
            'ville' => 12345
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer si la ville n'est pas une chaîne.");
        $this->assertArrayHasKey('ville', $validator->errors()->toArray());
    }

    public function test_generer_request_with_max_ville_fails_validation()
    {
        $data = [
            'type' => 'Appartement',
            'surface' => 100,
            'pieces' => 4,
            'ville' => 'azertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiopazertyuiop'
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer si la ville n'est pas une chaîne.");
        $this->assertArrayHasKey('ville', $validator->errors()->toArray());
    }







    // --------------------------------------------------------------
    //                  2 parameters empty
    // --------------------------------------------------------------

    public function test_generer_request_without_type_and_surface_fails_validation()
    {
        $data = [
            'pieces' => 4,
            'ville' => 'Bordeaux'
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer sans le type.");
        $this->assertArrayHasKey('type', $validator->errors()->toArray());
        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la surface.");
        $this->assertArrayHasKey('surface', $validator->errors()->toArray());
    }

    public function test_generer_request_without_type_and_ville_fails_validation()
    {
        $data = [
            'surface' => 100,
            'pieces' => 'quatre',
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer sans le type.");
        $this->assertArrayHasKey('type', $validator->errors()->toArray());
        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la ville.");
        $this->assertArrayHasKey('ville', $validator->errors()->toArray());
    }

    public function test_generer_request_without_type_and_piece_fails_validation()
    {
        $data = [
            'surface' => 100,
            'ville' => 'Bordeaux'
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer sans le type.");
        $this->assertArrayHasKey('type', $validator->errors()->toArray());
        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la piece.");
        $this->assertArrayHasKey('pieces', $validator->errors()->toArray());
    }

    public function test_generer_request_without_surface_and_piece_fails_validation()
    {
        $data = [
            'type' => 'Appartement',
            'ville' => 'Bordeaux'
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la surface.");
        $this->assertArrayHasKey('surface', $validator->errors()->toArray());
        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la piece.");
        $this->assertArrayHasKey('pieces', $validator->errors()->toArray());
    }

    public function test_generer_request_without_surface_and_ville_fails_validation()
    {
        $data = [
            'type' => 'Appartement',
            'pieces' => 'quatre',
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la surface.");
        $this->assertArrayHasKey('surface', $validator->errors()->toArray());
        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la ville.");
        $this->assertArrayHasKey('ville', $validator->errors()->toArray());
    }







    // --------------------------------------------------------------
    //                  3 parameters empty
    // --------------------------------------------------------------

    public function test_generer_request_without_type_and_surface_and_pieces_fails_validation()
    {
        $data = [
            'ville' => 'Bordeaux'
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer sans le type.");
        $this->assertArrayHasKey('type', $validator->errors()->toArray());
        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la surface.");
        $this->assertArrayHasKey('surface', $validator->errors()->toArray());
        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la piece.");
        $this->assertArrayHasKey('pieces', $validator->errors()->toArray());
    }

    public function test_generer_request_without_type_and_surface_and_villes_fails_validation()
    {
        $data = [
            'pieces' => 4,
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer sans le type.");
        $this->assertArrayHasKey('type', $validator->errors()->toArray());
        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la surface.");
        $this->assertArrayHasKey('surface', $validator->errors()->toArray());
        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la ville.");
        $this->assertArrayHasKey('ville', $validator->errors()->toArray());
    }

    public function test_generer_request_without_type_and_pieces_and_ville_fails_validation()
    {
        $data = [
            'surface' => 100,
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer sans le type.");
        $this->assertArrayHasKey('type', $validator->errors()->toArray());
        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la piece.");
        $this->assertArrayHasKey('pieces', $validator->errors()->toArray());
        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la ville.");
        $this->assertArrayHasKey('ville', $validator->errors()->toArray());
    }

    public function test_generer_request_without_surfaces_pieces_and_ville_fails_validation()
    {
        $data = [
            'type' => 'Appartement',
        ];

        $request = new GenererRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la surface.");
        $this->assertArrayHasKey('surface', $validator->errors()->toArray());
        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la piece.");
        $this->assertArrayHasKey('pieces', $validator->errors()->toArray());
        $this->assertTrue($validator->fails(), "La validation devrait échouer sans la ville.");
        $this->assertArrayHasKey('ville', $validator->errors()->toArray());
    }
}
