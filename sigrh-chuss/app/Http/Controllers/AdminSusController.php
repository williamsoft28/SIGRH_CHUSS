<?php

namespace App\Http\Controllers;

use App\Mail\IdentifiantsSusMail;
use App\Models\Service;
use App\Models\Sus;
use App\Models\User;
use App\Support\CompteSusGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminSusController extends Controller
{
    /**
     * Liste des comptes SUS existants.
     */
    public function index(): View
    {
        $comptes = User::role('sus')
            ->with('service')
            ->orderBy('nom')
            ->get();

        return view('admin.sus.index', compact('comptes'));
    }

    /**
     * Formulaire de création d'un compte SUS. Seuls les services sans SUS
     * sont proposés (un service ne peut avoir qu'un seul compte SUS).
     */
    public function create(): View
    {
        $services = Service::whereDoesntHave('suses')->orderBy('nom')->get();

        return view('admin.sus.create', compact('services'));
    }

    /**
     * Crée le compte SUS : l'identifiant et le mot de passe sont générés
     * automatiquement à partir du service choisi. Le service peut être
     * un service existant (non encore pourvu) ou un nouveau nom, auquel
     * cas le service est créé à la volée.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'matricule' => ['required', 'string', 'max:50', 'unique:users,matricule'],
            'service' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        ]);

        $service = Service::whereRaw('LOWER(nom) = ?', [Str::lower($data['service'])])->first()
            ?? Service::create([
                'nom' => $data['service'],
                'code_service' => $this->genererCodeService($data['service']),
                'type_service' => 'Non catégorisé',
            ]);

        if ($service->suses()->exists()) {
            return back()
                ->withErrors(['service' => 'Ce service a déjà un SUS.'])
                ->withInput();
        }

        $username = CompteSusGenerator::genererUsername($service);
        $motDePasse = CompteSusGenerator::genererMotDePasse();

        $user = User::create([
            'name' => "{$data['prenom']} {$data['nom']}",
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'matricule' => $data['matricule'],
            'username' => $username,
            'email' => $data['email'],
            'service_id' => $service->id,
            'password' => $motDePasse,
        ]);
        $user->assignRole('sus');

        Sus::create([
            'service_id' => $service->id,
            'user_id' => $user->id,
            'nom' => $user->name,
            'login' => $username,
        ]);

        $emailEnvoye = $this->envoyerIdentifiants($user, $username, $motDePasse, $service);

        return redirect()
            ->route('admin.sus.index')
            ->with('identifiants', [
                'nom' => $user->name,
                'username' => $username,
                'password' => $motDePasse,
                'email' => $user->email,
                'email_envoye' => $emailEnvoye,
            ]);
    }

    /**
     * Formulaire de modification d'un compte SUS.
     */
    public function edit(User $sus): View
    {
        abort_unless($sus->hasRole('sus'), 404);
        $services = Service::orderBy('nom')->get();

        return view('admin.sus.edit', compact('sus', 'services'));
    }

    /**
     * Met à jour un compte SUS.
     */
    public function update(Request $request, User $sus): RedirectResponse
    {
        abort_unless($sus->hasRole('sus'), 404);

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'matricule' => ['required', 'string', 'max:50', 'unique:users,matricule,'.$sus->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$sus->id],
            'service_id' => ['required', 'exists:services,id'],
        ]);

        $sus->update([
            'name' => "{$data['prenom']} {$data['nom']}",
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'matricule' => $data['matricule'],
            'email' => $data['email'],
            'service_id' => $data['service_id'],
        ]);

        // Mettre à jour le modèle Sus lié
        if ($sus->sus) {
            $sus->sus->update([
                'service_id' => $data['service_id'],
                'nom' => $sus->name,
            ]);
        }

        return redirect()
            ->route('admin.sus.index')
            ->with('status', 'Compte SUS mis à jour avec succès.');
    }

    /**
     * Supprime un compte SUS.
     */
    public function destroy(User $sus): RedirectResponse
    {
        abort_unless($sus->hasRole('sus'), 404);

        if ($sus->sus) {
            $sus->sus->delete();
        }
        $sus->delete();

        return redirect()
            ->route('admin.sus.index')
            ->with('status', 'Compte SUS supprimé avec succès.');
    }

    /**
     * Réinitialise le mot de passe d'un compte SUS (l'identifiant ne change pas).
     */
    public function reinitialiserMotDePasse(User $sus): RedirectResponse
    {
        abort_unless($sus->hasRole('sus'), 404);

        $motDePasse = CompteSusGenerator::genererMotDePasse();

        $sus->update(['password' => $motDePasse]);

        $emailEnvoye = $this->envoyerIdentifiants($sus, $sus->username, $motDePasse, $sus->service);

        return redirect()
            ->route('admin.sus.index')
            ->with('identifiants', [
                'nom' => $sus->name,
                'username' => $sus->username,
                'password' => $motDePasse,
                'email' => $sus->email,
                'email_envoye' => $emailEnvoye,
            ]);
    }

    /**
     * Envoie les identifiants à l'adresse email personnelle du SUS. Une erreur
     * d'envoi (SMTP indisponible, etc.) n'empêche pas la création du compte :
     * les identifiants restent affichables et transmissibles à la main.
     */
    private function envoyerIdentifiants(User $user, string $username, string $motDePasse, ?Service $service): bool
    {
        try {
            Mail::to($user->email)->send(new IdentifiantsSusMail(
                nom: $user->name,
                username: $username,
                motDePasse: $motDePasse,
                service: $service,
            ));

            return true;
        } catch (\Throwable $e) {
            Log::error("Échec de l'envoi des identifiants SUS à {$user->email} : {$e->getMessage()}");

            return false;
        }
    }

    /**
     * Code de service court et unique, dérivé du nom, pour les services
     * créés à la volée depuis la création d'un compte SUS.
     */
    private function genererCodeService(string $nom): string
    {
        $base = Str::upper(Str::slug($nom, '')) ?: 'SERVICE';
        $base = substr($base, 0, 20);

        $code = $base;
        $suffixe = 2;

        while (Service::where('code_service', $code)->exists()) {
            $code = substr($base, 0, 18).$suffixe;
            $suffixe++;
        }

        return $code;
    }
}
