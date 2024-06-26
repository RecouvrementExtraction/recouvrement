<?php

namespace App\Http\Controllers;

use App\Models\Commentaire;
use App\Models\MonModel;
use App\Models\ModelCompteT;
use App\Models\ModelCollaborateurs;
use App\Models\Portefeuille;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Recouvrement;
use PDF;

class MonController extends Controller
{
    public function montrer(Request $request){
        $dataBase = MonModel::select(
            'JM_Date',
            'JO_Num',
             'CT_Num',
              'EC_RefPiece',
               'EC_Intitule',
                'CG_Num',
                 'EC_Echeance',
                  'EC_Sens',
                   'EC_Montant',
                    'EC_Lettrage')
            ->get();
            return view('juste',compact('dataBase'));


}


    public function faux(Request $request){
        $data = ModelCompteT ::join('F_ECRITUREC', 'F_COMPTET.CT_Num', '=', 'F_ECRITUREC.CT_Num')
        ->join('F_COLLABORATEUR', 'F_COMPTET.CO_No', '=', 'F_COLLABORATEUR.CO_No')
        ->select('F_COMPTET.CO_No','F_COLLABORATEUR.CO_Nom', 'F_ECRITUREC.CT_Num', 'F_ECRITUREC.EC_Intitule', 'F_ECRITUREC.EC_sens', 'F_ECRITUREC.Ec_Montant', 'F_ECRITUREC.EC_Echeance')
        ->where('F_ECRITUREC.CT_Num', 'like', 'CL%')
        ->orderBy('F_ECRITUREC.CT_Num')
        ->whereYear('EC_Echeance','2023')
        ->get();

        return view('faux',compact('data'));
    }



    public function details(Request $request, $CT_Num)
    {
        $query = $request->input('search');
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
            ->where('F_ECRITUREC.CT_Num', '=', $CT_Num)
            ->where('F_ECRITUREC.EC_Lettre', '=', 0)
            ->where('F_JOURNAUX.JO_Type', '=', 1)
            ->where('F_ECRITUREC.CT_Num', 'like', 'CL%')
            ->when($query, function ($q) use ($query) {
                return $q->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('F_COMPTET.CT_Intitule', 'like', "%$query%")
                        ->orWhere('F_COMPTET.CT_Telephone', 'like', "%$query%")
                        ->orWhere('F_COMPTET.CT_EMail', 'like', "%$query%")
                        ->orWhere('F_COLLABORATEUR.CO_Nom', 'like', "%$query%")
                        ->orWhere('F_ECRITUREC.EC_Intitule', 'like', "%$query%");
                });
            })
            ->paginate(10); // Utilisation de paginate pour la pagination

            $solde = $this->calculerSolde($data);

            // Passer les données et le solde à la vue
            return view('details', compact('data', 'solde','CT_Num'));
        // return view('details', compact('data', 'CT_Num'));
    }






    public function facturesClient(Request $request, $CT_Num)
    {
        $query = $request->input('search');
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
                'F_ECRITUREC.EC_Lettre',
            )
            ->where('F_ECRITUREC.CT_Num', '=', $CT_Num)
            ->where('F_ECRITUREC.EC_Lettre', '=', 0)
            ->where('F_ECRITUREC.CT_Num', 'like', 'CL%')
            ->when($query, function ($q) use ($query) {
                return $q->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('F_COMPTET.CT_Intitule', 'like', "%$query%")
                        ->orWhere('F_COMPTET.CT_Telephone', 'like', "%$query%")
                        ->orWhere('F_COMPTET.CT_EMail', 'like', "%$query%")
                        ->orWhere('F_COLLABORATEUR.CO_Nom', 'like', "%$query%")
                        ->orWhere('F_ECRITUREC.EC_Intitule', 'like', "%$query%");
                });
            })
            ->paginate(10);

            return view('facturesClient', compact('data','CT_Num'));

    }



    private function calculerSolde($var)
    {
        $solde = 0;

        // Parcourir les données pour calculer le solde
        foreach ($var as $item) {
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

     // Supposons que $soldesParClient a déjà été rempli avec les détails des clients

                // foreach ($soldesParClient as $CT_Num => &$client) {
                //     // Chercher la correspondance dans $recouvrements pour mettre à jour le solde
                //     foreach ($recouvrements as $recouvrement) {
                //         if ($recouvrement->idClient == $CT_Num) {
                //             // Mettre à jour le solde courant avec le solde de la nouvelle requête
                //             $client['total'] -= $recouvrement->solde;
                //             break; // Sortir de la boucle dès qu'on a trouvé la correspondance
                //         }
                //     }
                // }


    public function fusion()
    {
        $data = ModelCompteT ::join('F_ECRITUREC', 'F_COMPTET.CT_Num', '=', 'F_ECRITUREC.CT_Num')
        ->join('F_COLLABORATEUR', 'F_COMPTET.CO_No', '=', 'F_COLLABORATEUR.CO_No')
        ->select('F_COMPTET.CO_No','F_COLLABORATEUR.CO_Nom', 'F_ECRITUREC.CT_Num', 'F_ECRITUREC.EC_Intitule', 'F_ECRITUREC.EC_sens', 'F_ECRITUREC.Ec_Montant', 'F_ECRITUREC.EC_Echeance')
        ->where('F_ECRITUREC.CT_Num', 'like', 'CL%')
        ->orderBy('F_ECRITUREC.CT_Num')
        ->whereYear('EC_Echeance','2023')
        ->get();

        return view('jointure',compact('data'));
    }


    public function peuplerPortefeuilleAvecCO_No()
    {
        // Récupérez les données de la colonne "CO_No" depuis la table "F_CompteT" avec la condition whereYear
        $donneesCO_No = ModelCollaborateurs::select('CO_Nom')->pluck('CO_Nom');

        // Parcourez les données et créez des enregistrements dans la table "portefeuille" uniquement s'ils n'existent pas déjà
        foreach ($donneesCO_No as $coNo) {
            if (!Portefeuille::where('name', $coNo)->exists()) {
                $portefeuille = new Portefeuille();
                $portefeuille->name = $coNo;
                $portefeuille->save();
            }
        }

        return "Mise à jour de la colonne name de la table Portefeuille.";
    }

    public function enregistrerLigne(Request $request)
    {
        $request->validate([
            'id_agent' => 'required',
            'idClient' => 'required',
        ]);


        $id_agent = $request->input('id_agent');
        $idClient = $request->input('idClient');
        $ligne = $request->input('ligne');
        $email = $request->input('email');
        $num_facture = $request->input('num_facture');
        $libelle = $request->input('libelle');
        $telephone = $request->input('telephone');
        $credit = $request->input('credit');
        $debit = $request->input('debit');
        $debit = $request->input('debit');


        $libelle = $request->input('libelle');
        // Vérifier si un enregistrement avec le même 'libelle' existe déjà
        $existingRecord = DB::table('recouvrements')
        ->where('libelle', $libelle)
        ->first();

        if (!$existingRecord) {
            // Gérer le cas où un enregistrement avec le même 'libelle' existe déjà

        DB::transaction(function () use ($ligne, $idClient, $libelle, $email, $telephone, $num_facture, $credit, $debit, $id_agent) {
            DB::table('recouvrements')->insert([
                'ligne' => $ligne,
                'idClient' => $idClient,
                'libelle' => $libelle,
                'email' => $email,
                'telephone' => $telephone,
                'num_facture' => $num_facture,
                'credit' => $credit,
                'debit' => $debit,
                'id_agent' => $id_agent,
            ]);
        });

    // Ajouter le libellé à la liste des libellés ajoutés
    $addedLibelles = session('addedLibelles', []);
    $addedLibelles[] = $libelle;
    session(['addedLibelles' => $addedLibelles]);

    return redirect()->back()->with('message', 'Enregistrement inséré avec succès.');
        }  return redirect()->back()->with('message', 'Un enregistrement avec le même libellé existe déjà.');
    }





    public function enregistrer_commentaire(Request $request){
        $id_agent = $request->input('id_agent');
        $idClient = $request->input('idClient');
        $ligne = $request->input('ligne');
        $email = $request->input('email');
        $num_facture = $request->input('num_facture');
        $libelle = $request->input('libelle');
        $telephone = $request->input('telephone');
        $message = $request->input('message');
        $credit = $request->input('credit');
        $debit = $request->input('debit');

        DB::table('commentaires')->insert([
            'ligne' => $ligne,
            'idClient' => $idClient,
            'libelle' => $libelle,
            'email' => $email,
            'telephone' => $telephone,
            'num_facture' => $num_facture,
            'credit' => $credit,
            'debit' => $debit,
            'message' => $message,
            'id_agent' => $id_agent,
        ]);

        return redirect()->back()->with('message', 'Message ajouter.');
    }


    // public function enregistrer_commentaire(Request $request){
    //     $id_agent = $request->input('id_agent');
    //     $idClient = $request->input('idClient');
    //     $ligne = $request->input('ligne');
    //     $email = $request->input('email');
    //     $num_facture = $request->input('num_facture');
    //     $libelle = $request->input('libelle');
    //     $telephone = $request->input('telephone');
    //     $message = $request->input('message');
    //     $credit = $request->input('credit');
    //     $debit = $request->input('debit');

    //     // Affichage des données dans la console
    //     dd([
    //         'ligne' => $ligne,
    //         'idClient' => $idClient,
    //         'libelle' => $libelle,
    //         'email' => $email,
    //         'telephone' => $telephone,
    //         'num_facture' => $num_facture,
    //         'credit' => $credit,
    //         'debit' => $debit,
    //         'message' => $message,
    //         'id_agent' => $id_agent,
    //     ]);
    // }


    // À chaque lancement de l'application
    public function lancementApplication()
    {
        // Récupérer tous les libellés présents dans la base de données
        $libellesMasques = DB::table('recouvrements')->pluck('libelle')->toArray();

        // Mettre à jour la session pour refléter l'état de masquage
        session(['addedLibelles' => $libellesMasques]);

    }


    public function client_recouvre($id){
        $data = Recouvrement::all()->where("id_agent", "=", $id);
        return view('les_recouvres', compact('data'));
    }

    public function client_rappel($id){
        $data = Commentaire::all()->where("id_agent", "=", $id);
        return view('rappels', compact('data'));
    }


    public function supprimer_ligne($id)
    {
        // Trouver la ligne par son ID et la supprimer
        $ligne = Commentaire::find($id);

        if ($ligne) {
            $ligne->delete();
            return redirect()->back()->with('success', 'Ligne supprimée avec succès.');
        }

        return redirect()->back()->with('error', 'Ligne non trouvée.');
    }



    public function viens(Request $request){
        $id_agent = $request->input('id_agent');
        $idClient = $request->input('idClient');
        $ligne = $request->input('ligne');
        $email = $request->input('email');
        $num_facture = $request->input('num_facture');
        $libelle = $request->input('libelle');
        $telephone = $request->input('telephone');
        $credit = $request->input('credit');
        $debit = $request->input('debit');

        // Utilisation de dd() pour afficher les valeurs
        dd([
            'id_agent' => $id_agent,
            'idClient' => $idClient,
            'ligne' => $ligne,
            'email' => $email,
            'num_facture' => $num_facture,
            'libelle' => $libelle,
            'telephone' => $telephone,
            'credit' => $credit,
            'debit' => $debit,
        ]);
    }
}

