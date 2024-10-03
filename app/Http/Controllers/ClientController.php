<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\ClientRepository;
use Yajra\DataTables\Facades\DataTables;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    // Menampilkan view utama
    public function index()
    {
        return view('home');
    }

    // Mengambil data client untuk server-side DataTables
    public function getClients(Request $request)
    {
        if ($request->ajax()) {
            $clients = Client::select(['id', 'user_id', 'name', 'secret', 'provider', 'redirect']);
            
            return DataTables::of($clients)
                ->addColumn('action', function ($client) {
                    return '
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-sm btn-warning btn-edit mx-2" data-id="' . $client->id . '" data-toggle="modal" data-target="#editModal" title="Edit">
                            <i class="fas fa-edit text-white"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete mx-2" data-id="' . $client->id . '" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    ';
                })
                ->rawColumns(['action']) // Pastikan HTML aman untuk diproses sebagai kolom action
                ->make(true);
        }
    }


    // Fungsi untuk membuat client baru
    public function createClient(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'redirect' => 'required|url',
        ]);

        // Menggunakan ClientRepository dari Laravel Passport
        $clientRepository = new ClientRepository();

        // Membuat OAuth Client dengan Passport
        $client = $clientRepository->create(
            Auth::user()->getKey(), // Mengambil ID user yang sedang login
            $request->name,         // Nama client
            $request->redirect,     // Redirect URI
            null,                   // Provider, bisa null jika tidak menggunakan provider
            false,                  // Apakah ini personal access client? (false)
            false                   // Apakah ini password grant client? (false)
        );

        // Mengembalikan response dengan client_id dan client_secret yang di-generate
        // return response()->json([
        //     'message' => 'Client created successfully',
        //     'client_id' => $client->id,
        //     'client_secret' => $client->secret // Client secret yang dihasilkan oleh Passport
        // ], 201);
                return redirect()->back()->with('status', 'Client created successfully!');

    }

    // Fungsi untuk menghapus client dengan AJAX
    public function deleteClient($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json(['message' => 'Client deleted successfully']);
    }

    // Mengambil data client untuk diedit dengan AJAX
    public function editClient($id)
    {
        $client = Client::findOrFail($id);
        return response()->json($client);
    }

    // Fungsi untuk mengupdate client dengan AJAX
    public function updateClient(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'redirect' => 'required|url',
        ]);

        $client = Client::findOrFail($id);
        $client->name = $request->name;
        $client->redirect = $request->redirect;
        $client->save();

        return response()->json(['message' => 'Client updated successfully']);
    }
}
