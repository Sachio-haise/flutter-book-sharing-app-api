<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Media\Storage;
use App\Http\Resources\BookResource;
use App\Http\Resources\CartResource;
use App\Models\Book;
use App\Models\Cart;
use App\Models\Reaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{

    public function getBooks()
    {
        try {
            $books = Book::latest()->get();
            return response()->json(['data' => BookResource::collection($books)], 200);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }

    public function getCart(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $books = Cart::where('user_id', $request->user_id)->latest()->get();
            return response()->json(['data' => CartResource::collection($books)], 200);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }

    public function addBook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required',
            'name' => 'required|max:255',
            'description' => 'required',
            'review' => 'required',
            'photo' => 'required',
            'book' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {

            $folderName = 'book_' . $request->name . '_' . mt_rand(100000, 999999);
            if ($request->hasFile('photo')) {
                $response = Storage::upload($request->photo, Book::class, null, $folderName);
                $photo = $response['data']['id'] ?? null;
            } else {
                $photo = null;
            }

            if ($request->hasFile('book')) {
                $response = Storage::upload($request->book, Book::class, null, $folderName);
                $book = $response['data']['id'] ?? null;
            } else {
                $book = null;
            }

            $data = [
                'user_id' => $request->user,
                'name' => $request->name,
                'description' => $request->description,
                'review' => $request->review,
                'photo_id' => $photo,
                'book_id' => $book
            ];
            $book = Book::create($data);
            return response()->json(['data' => new BookResource($book), 'message' => 'Book added successfully!'], 200);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }

    public function updateBook(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $book = Book::findOrFail($id);
            if ($request->hasFile('book')) {
                $response = Storage::upload($request->book, Book::class, $book->book_id, $request->book);
                if ($response['status'] === 1) {
                    $media_id = $response['data']['id'] ?? null;
                    $book->book_id = $media_id;
                } else {
                    throw new Exception($response['message']);
                }
            }

            if ($request->hasFile('photo')) {
                $response = Storage::upload($request->photo, Book::class, $book->photo_id, $request->photo);
                if ($response['status'] === 1) {
                    $media_id = $response['data']['id'] ?? null;
                    $book->photo_id = $media_id;
                } else {
                    throw new Exception($response['message']);
                }
            }

            $book->name = $request->name;
            $book->description = $request->description;
            $book->review = $request->review;
            $book->save();
            return response()->json(['data' => new BookResource($book), 'message' => 'Book modified successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }


    public function deleteBook($id)
    {
        try {
            $book = Book::findOrFail($id);
            $book->delete();
            return response()->json(['data' => $book, 'message' => 'Book deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }

    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'book_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try{
            $cart = Cart::where('book_id', $request->book_id)->where('user_id', $request->user_id)->first();

            if($cart){
                $cart->delete();
                return response()->json(['message' => 'Book removed from cart successfully'], 200);
            }else{
                $cart = new Cart();
                $cart->user_id = $request->user_id;
                $cart->book_id = $request->book_id;
                $cart->save();
                return response()->json(['message' => 'Book added to cart successfully'], 200);
            }
        }catch(Exception $e){
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }

    public function reactBook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $reaction = Reaction::where('book_id', $request->book_id)->where('user_id', $request->user_id)->first();
            if ($reaction) {
                $reaction->delete();
                return response()->json(['message' => 'Book unreacted successfully'], 200);
            } else {
                $reaction = new Reaction();
                $reaction->book_id = $request->book_id;
                $reaction->user_id = $request->user_id;
                $reaction->save();
                return response()->json(['message' => 'Book reacted successfully'], 200);
            }
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }
}
