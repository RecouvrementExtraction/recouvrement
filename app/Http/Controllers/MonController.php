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
use Auth;
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

        // Récupérer les données paginées
        $data = DB::table('F_ECRITUREC')
            ->join('F_COMPTET', 'F_ECRITUREC.CT_Num', '=', 'F_COMPTET.CT_Num')
            ->join('F_JOURNAUX', 'F_ECRITUREC.JO_Num', '=', 'F_JOURNAUX.JO_Num')
            ->join('F_COLLABORATEUR', 'F_COMPTET.CO_No', '=', 'F_COLLABORATEUR.CO_No')
            ->join('portefeuilles', 'F_COLLABORATEUR.CO_Nom', '=', 'portefeuilles.name')
            ->join('portefeuille_user', 'portefeuilles.id', '=', 'portefeuille_user.portefeuille_id')
            ->join('users', 'portefeuille_user.user_id', '=', 'users.id')
            ->leftJoin('recouvrements', 'F_ECRITUREC.EC_RefPiece', '=', 'recouvrements.num_facture')
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
            ->whereNull('recouvrements.num_facture')
            ->when($query, function ($q) use ($query) {
                return $q->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('F_COMPTET.CT_Intitule', 'like', "%$query%")
                        ->orWhere('F_COMPTET.CT_Telephone', 'like', "%$query%")
                        ->orWhere('F_COMPTET.CT_EMail', 'like', "%$query%")
                        ->orWhere('F_COLLABORATEUR.CO_Nom', 'like', "%$query%")
                        ->orWhere('F_ECRITUREC.EC_Intitule', 'like', "%$query%");
                });
            })
            ->orderBy('F_ECRITUREC.EC_RefPiece')
            ->paginate(10);

        // Récupérer toutes les données pour le calcul du solde total
        $allData = DB::table('F_ECRITUREC')
            ->join('F_COMPTET', 'F_ECRITUREC.CT_Num', '=', 'F_COMPTET.CT_Num')
            ->join('F_JOURNAUX', 'F_ECRITUREC.JO_Num', '=', 'F_JOURNAUX.JO_Num')
            ->join('F_COLLABORATEUR', 'F_COMPTET.CO_No', '=', 'F_COLLABORATEUR.CO_No')
            ->join('portefeuilles', 'F_COLLABORATEUR.CO_Nom', '=', 'portefeuilles.name')
            ->join('portefeuille_user', 'portefeuilles.id', '=', 'portefeuille_user.portefeuille_id')
            ->join('users', 'portefeuille_user.user_id', '=', 'users.id')
            ->leftJoin('recouvrements', 'F_ECRITUREC.EC_RefPiece', '=', 'recouvrements.num_facture')
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
            ->whereNull('recouvrements.num_facture')
            ->get();

        // Calculer le solde total
        $totalSolde = 0;
        foreach ($allData as $donnee) {
            $debitValue = ($donnee->EC_sens <= 0) ? $donnee->Ec_Montant : 0;
            $creditValue = ($donnee->EC_sens > 0) ? $donnee->Ec_Montant : 0;
            $totalSolde += ($debitValue - $creditValue);
        }

        // Passer les données et le solde à la vue
        return view('details', compact('data', 'totalSolde', 'CT_Num'));
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

        // Récupération des données du formulaire
        $data = $request->only([
            'ligne', 'idClient', 'libelle', 'email', 'telephone',
            'num_facture', 'credit', 'debit', 'id_agent'
        ]);

        // Vérifier si un enregistrement avec le même 'num_facture' existe déjà
        $existingRecord = Recouvrement::where('num_facture', $data['num_facture'])->first();

        if (!$existingRecord) {
            // Utilisation du modèle Eloquent pour créer un nouvel enregistrement
            $recouvrement = new Recouvrement();
            $recouvrement->fill($data);
            $recouvrement->save();

            // Ajouter le num_facture à la liste des numéros ajoutés
            $addedNumFactures = session('addedNumFactures', []);
            $addedNumFactures[] = $data['num_facture'];
            session(['addedNumFactures' => $addedNumFactures]);

            return redirect()->back()->with('message', 'Enregistrement inséré avec succès.');
        } else {
            return redirect()->back()->with('message', 'Un enregistrement avec le même num_facture existe déjà.');
        }
    }






    public function enregistrer_commentaire(Request $request)
    {
        $data = $request->only([
            'id_agent',
            'idClient',
            'ligne',
            'email',
            'num_facture',
            'libelle',
            'telephone',
            'message',
            'credit',
            'debit',
        ]);

        // dd($data);

        Commentaire::create($data);

        return redirect()->back()->with('message', 'Message ajouté.');
    }





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

    public function client_rappel($id)
{
    // Récupérer les commentaires pour un agent spécifique en excluant ceux dont le num_facture est dans recouvrements
    $data = DB::table('commentaires')
        ->where('id_agent', '=', $id)
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('recouvrements')
                  ->whereRaw('recouvrements.num_facture = commentaires.num_facture');
        })
        ->select('commentaires.*') // Sélectionnez les colonnes que vous souhaitez obtenir
        ->get();
        // dd($data);

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





}

