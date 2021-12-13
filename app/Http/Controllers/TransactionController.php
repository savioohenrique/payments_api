<?php

namespace App\Http\Controllers;

use App\Exceptions\AccountNotFoundException;
use App\Exceptions\InvalidUserTypeException;
use App\Exceptions\NotEnoughMoneyException;
use App\Exceptions\RefusedTransactionException;
use App\Exceptions\UnavailableServiceException;
use App\Exceptions\UserNotFoundException;
use App\Services\Transaction\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    /**
     * Class constructor.
     */
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Realize transaction between two users.
     * 
     * @OA\Post(
     *     path="/transaction",
     *     tags={"Transaction"},
     *     description="Realize transction between users and return transaction data.",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="=User credentials",
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="value", type="decimal", format="text", example="150.25"),
     *             @OA\Property(property="provider", type="string", format="text", example="storekeeper"),
     *             @OA\Property(property="payee", type="string", format="email", example="storekeeper@email.com"),
     *         ),
     *    ),
     *     @OA\Response(
     *         response=200, 
     *         description="Realized transaction"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Refused transaction"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid parameters"
     *     ),
     * )
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function realizeTransaction(Request $request)
    {
        try {
            $this->validate($request,['value' => 'required|numeric|min:0.01', 'provider' => 'required|string' , 'payee' => 'required|email']);
            $result = $this->transactionService->realizeTransaction($request);
            
            return response()->json($result, 200);
        } catch (RefusedTransactionException | UnavailableServiceException | NotEnoughMoneyException $exception) {
            return response()->json(['errors' => ['main' => $exception->getMessage()]], 403);
        } catch (UserNotFoundException | AccountNotFoundException $exception) {
            return response()->json(['errors' => ['main' => $exception->getMessage()]], 404);
        } catch (InvalidUserTypeException | ValidationException $exception) {
            return response()->json(['errors' => ['main' => $exception->getMessage()]], 422);
        }
    }

}
