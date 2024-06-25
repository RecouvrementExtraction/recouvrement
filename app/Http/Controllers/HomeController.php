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
        if (Auth::check()) {
            $userId = Auth::id();
            $query = $request->input('search');

            $row = DB::table('recouvrements')->select('idClient', 'credit', 'debit');

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

                // Vérifie le EC_sens pour déterminer le débit ou le crédit
                if ($item->EC_sens > 0) {
                    $soldesParClient[$CT_Num]['totalCredit'] += $item->Ec_Montant;
                } else {
                    $soldesParClient[$CT_Num]['totalDebit'] += $item->Ec_Montant;
                }

                // Ajouter le détail de la facture
                $found = false;
                foreach ($soldesParClient[$CT_Num]['details'] as &$detail) {
                    if ($detail['EC_Intitule'] === $item->EC_Intitule) {
                        $detail['Ec_Montant'] += $item->Ec_Montant;
                        $found = true;
                        break;
                    }
                }
                unset($detail); // Dissocier la référence pour éviter les effets indésirables

                if (!$found) {
                    $soldesParClient[$CT_Num]['details'][] = [
                        'EC_Intitule' => $item->EC_Intitule,
                        'Ec_Montant' => $item->Ec_Montant,
                        'EC_Lettre' => $item->EC_Lettre,
                    ];
                }

                // Calculer le total
                $soldesParClient[$CT_Num]['total'] = $soldesParClient[$CT_Num]['totalCredit'] - $soldesParClient[$CT_Num]['totalDebit'];
            }

            $recouvrements = DB::table('recouvrements')
                ->select('idClient',
                    DB::raw('SUM(CAST(credit AS FLOAT)) as total_credit'),
                    DB::raw('SUM(CAST(debit AS FLOAT)) as total_debit'),
                    DB::raw('SUM(CAST(credit AS FLOAT)) - SUM(CAST(debit AS FLOAT)) as solde'))
                ->groupBy('idClient')
                ->get();

             foreach ($soldesParClient as $CT_Num => &$client) {
                    // Chercher la correspondance dans $recouvrements pour mettre à jour le solde
                    foreach ($recouvrements as $recouvrement) {
                        if ($recouvrement->idClient == $CT_Num) {
                            // Mettre à jour le solde courant avec le solde de la nouvelle requête
                            $client['total'] -= $recouvrement->solde;
                            break; // Sortir de la boucle dès qu'on a trouvé la correspondance
                        }
                    }
                }

            return view('home', compact('data', 'recouvrements', 'soldesParClient'));
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
