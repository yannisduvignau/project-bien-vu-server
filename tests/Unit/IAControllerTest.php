<?php
namespace Tests\Unit;

use App\Actions\OpenAIRequestAction;
use App\Http\Controllers\IAController;
use App\Http\Requests\AnalyserRequest;
use App\Http\Requests\EstimerRequest;
use App\Http\Requests\GenererRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Mockery;
use PHPUnit\Framework\TestCase;

class IAControllerTest extends TestCase
{

    public function test_analyserAnnonce_success()
    {

    }

    public function test_analyserAnnonce_missing_env()
    {

    }

    public function test_estimerPrix_success()
    {

    }

    public function test_estimerPrix_missing_env()
    {

    }

    public function test_genererAnnonce_success()
    {

    }

    public function test_genererAnnonce_missing_env()
    {

    }

    // Tests de gestion des erreurs
    public function test_analyserAnnonce_invalid_response_format()
    {

    }

    public function test_estimerPrix_invalid_response_format()
    {

    }

    public function test_genererAnnonce_invalid_response_format()
    {

    }

}
