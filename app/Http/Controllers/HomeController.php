<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index()
    // {
    //     $data = ModelCompteT ::join('F_ECRITUREC', 'F_COMPTET.CT_Num', '=', 'F_ECRITUREC.CT_Num')
    //     ->join('F_COLLABORATEUR', 'F_COMPTET.CO_No', '=', 'F_COLLABORATEUR.CO_No')
    //     ->select('F_COMPTET.CO_No','F_COLLABORATEUR.CO_Nom', 'F_ECRITUREC.CT_Num', 'F_ECRITUREC.EC_Intitule', 'F_ECRITUREC.EC_sens', 'F_ECRITUREC.Ec_Montant', 'F_ECRITUREC.EC_Echeance')
    //     ->where('F_ECRITUREC.CT_Num', 'like', 'CL%')
    //     ->orderBy('F_ECRITUREC.CT_Num')
    //     // ->whereYear('EC_Echeance','2023')
    //     ->get();
    //     $moa = "Monace-King";
    //     return view('home', compact('moa', 'data', 'moa'));
    // }


    public function index(Request $request)
    {
        // Vérifiez d'abord si un utilisateur est connecté
        if (Auth::check()) {
            // Récupérez l'ID de l'utilisateur connecté
            $userId = Auth::id();
            $query = $request->input('search');

            // Utiliser $userId dans la requête
            $row = DB::table('recouvrements')->select('idClient','credit','debit');
            $data = DB::table('F_ECRITUREC')
                ->join('F_COMPTET', 'F_ECRITUREC.CT_Num', '=', 'F_COMPTET.CT_Num')
                ->join('F_COLLABORATEUR', 'F_COMPTET.CO_No', '=', 'F_COLLABORATEUR.CO_No')
                ->join('portefeuilles', 'F_COLLABORATEUR.CO_Nom', '=', 'portefeuilles.name')
                ->join('portefeuille_user', 'portefeuilles.id', '=', 'portefeuille_user.portefeuille_id')
                ->join('users', 'portefeuille_user.user_id', '=', 'users.id')
                ->select(
                    'F_COMPTET.CO_No',
                    'F_COMPTET.CT_Intitule',
                    'F_COMPTET.CT_Telephone',
                    'F_COMPTET.CT_EMail',
                    'F_COLLABORATEUR.CO_Nom',
                    'F_ECRITUREC.CT_Num',
                    'F_ECRITUREC.EC_Intitule',
                    'F_ECRITUREC.EC_sens',
                    'F_ECRITUREC.Ec_Montant',
                    'F_ECRITUREC.EC_Echeance',
                    'F_ECRITUREC.EC_RefPiece',
                    'F_ECRITUREC.EC_Lettre'
                )
                ->where('F_ECRITUREC.EC_Lettre', '=', 0)
                ->where('F_ECRITUREC.CT_Num', 'like', 'CL%')
                ->where('users.id', '=', $userId)
                ->when($query, function ($q) use ($query) {
                    return $q->where(function ($queryBuilder) use ($query) {
                        $queryBuilder->where('F_COMPTET.CT_Intitule', 'like', "%$query%")
                            ->orWhere('F_COMPTET.CT_Telephone', 'like', "%$query%")
                            ->orWhere('F_COMPTET.CT_EMail', 'like', "%$query%")
                            ->orWhere('F_COLLABORATEUR.CO_Nom', 'like', "%$query%")
                            ->orWhere('F_ECRITUREC.EC_Intitule', 'like', "%$query%");
                    });
                })
                ->orderBy('F_ECRITUREC.CT_Num')
                ->paginate(10); // Pagination à 10 éléments par page

                 // Calculer le solde total
    $solde = $this->calculerSolde($data);


    $recouvrements = DB::table('recouvrements')
    ->select('idClient',
             DB::raw('SUM(CAST(credit AS FLOAT)) as total_credit'),
             DB::raw('SUM(CAST(debit AS FLOAT)) as total_debit'),
             DB::raw('SUM(CAST(credit AS FLOAT)) - SUM(CAST(debit AS FLOAT)) as solde'))
    ->groupBy('idClient')
    ->get();


    $recouvrementSolde = [];

    // Exemple : bouclez à travers $recouvrements pour obtenir les soldes par CT_Num
    foreach ($recouvrements as $recouvrement) {
        $recouvrementSolde[$recouvrement->idClient] = $recouvrement->solde;
    }
    return view('home', compact('data', 'solde','recouvrements', 'recouvrementSolde'));

        } else {
            return redirect()->back();
        }
    }


    private function calculerSolde($data)
{
    $solde = 0;

    // Parcourir les données pour calculer le solde
    foreach ($data as $item) {
        if ($item->EC_sens > 0) {
            // Si EC_sens est positif, c'est un crédit
            $solde += $item->Ec_Montant;
        } else {
            // Sinon, c'est un débit
            $solde -= $item->Ec_Montant;
        }
    }

    return $solde;
}
}
