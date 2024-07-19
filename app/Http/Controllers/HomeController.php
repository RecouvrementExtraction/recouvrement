<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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


     public function index(Request $request)
     {
         if (Auth::check()) {
             $userId = Auth::id();
             $query = $request->input('search');

             // Étape 1: Récupérer toutes les données nécessaires
             $data = DB::table('F_ECRITUREC')
                 ->join('F_COMPTET', 'F_ECRITUREC.CT_Num', '=', 'F_COMPTET.CT_Num')
                 ->join('F_JOURNAUX', 'F_ECRITUREC.JO_Num', '=', 'F_JOURNAUX.JO_Num')
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
                     'F_ECRITUREC.EC_Lettre',
                     'F_JOURNAUX.JO_Type'
                 )
                 ->where('F_ECRITUREC.EC_Lettre', '=', 0)
                 ->where('F_JOURNAUX.JO_Type', '=', 1)
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
                 ->get();

             // Étape 2: Calculer les soldes par client
             $soldesParClient = [];

             foreach ($data as $item) {
                 $CT_Num = $item->CT_Num;

                 if (!isset($soldesParClient[$CT_Num])) {
                     $soldesParClient[$CT_Num] = [
                         'CT_Intitule' => $item->CT_Intitule,
                         'CT_Telephone' => $item->CT_Telephone,
                         'CT_EMail' => $item->CT_EMail,
                         'CO_Nom' => $item->CO_Nom,
                         'details' => [],
                         'totalDebit' => 0,
                         'totalCredit' => 0,
                         'total' => 0,
                     ];
                 }

                 if ($item->EC_sens > 0) {
                     $soldesParClient[$CT_Num]['totalCredit'] += $item->Ec_Montant;
                 } else {
                     $soldesParClient[$CT_Num]['totalDebit'] += $item->Ec_Montant;
                 }

                 $soldesParClient[$CT_Num]['details'][] = [
                     'EC_Intitule' => $item->EC_Intitule,
                     'Ec_Montant' => $item->Ec_Montant,
                     'EC_Lettre' => $item->EC_Lettre,
                 ];

                 $soldesParClient[$CT_Num]['total'] = $soldesParClient[$CT_Num]['totalCredit'] - $soldesParClient[$CT_Num]['totalDebit'];
             }

             // Calculer le solde global
             $soldeGlobal = abs($this->recalculerSoldeGlobal($soldesParClient));

             // Étape 3: Récupérer le total des débits dans la table "recouvrements"
             $totalDebitsRecouvrements = DB::table('recouvrements')
                 ->sum('debit');

                //  dd($soldeGlobal, $totalDebitsRecouvrements);
             // Soustraire les débits des recouvrements du solde global
             $soldeGlobal -= $totalDebitsRecouvrements;

             // Étape 4: Ajouter les recouvrements
             $recouvrements = DB::table('recouvrements')
                 ->select('idClient',
                     DB::raw('SUM(CAST(credit AS FLOAT)) as total_credit'),
                     DB::raw('SUM(CAST(debit AS FLOAT)) as total_debit'),
                     DB::raw('SUM(CAST(credit AS FLOAT)) - SUM(CAST(debit AS FLOAT)) as solde'))
                 ->groupBy('idClient')
                 ->get();

             foreach ($soldesParClient as $CT_Num => &$client) {
                 foreach ($recouvrements as $recouvrement) {
                     if ($recouvrement->idClient == $CT_Num) {
                         $client['total'] -= $recouvrement->solde;
                         break;
                     }
                 }
             }

             // Étape 5: Paginer les résultats manuellement LengthAwarePaginator
             $perPage = 10;
             $currentPage = LengthAwarePaginator::resolveCurrentPage();
             $clientCollection = collect($soldesParClient);
             $currentPageClients = $clientCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();
             $paginatedClients = new LengthAwarePaginator($currentPageClients, $clientCollection->count(), $perPage);

             $paginatedClients->setPath($request->url());
             $paginatedClients->appends($request->all());

             // Étape 6: Retourner la vue avec toutes les données
             return view('home', [
                 'data' => $data,
                 'recouvrements' => $recouvrements,
                 'soldesParClient' => $paginatedClients,
                 'soldeGlobal' => $soldeGlobal,
                 'totalDebitsRecouvrements' => $totalDebitsRecouvrements,
             ]);
         } else {
             return redirect()->back();
         }
     }

     /**
      * Recalculer le solde global en fonction des soldes par client actuels.
      *
      * @param array $soldesParClient
      * @return float
      */
     private function recalculerSoldeGlobal($soldesParClient)
     {
         return collect($soldesParClient)->sum(function ($client) {
             return $client['total'];
         });
     }










}
