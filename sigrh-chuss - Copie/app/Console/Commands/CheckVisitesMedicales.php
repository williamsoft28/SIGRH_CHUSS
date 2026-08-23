<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\VisiteMedicale;
use App\Notifications\VisiteMedicaleDepassee;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckVisitesMedicales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visites:check-depassees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie les visites médicales programmées dont la date est dépassée et alerte les chefs de service.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $visitesDepassees = VisiteMedicale::where('statut', 'programmee')
            ->whereDate('date_programmee', '<', Carbon::today())
            ->with('user.service.users') // Pour trouver le chef de service
            ->get();
            
        $count = 0;

        foreach ($visitesDepassees as $visite) {
            // Mettre à jour le statut
            $visite->update(['statut' => 'depassee']);
            $count++;

            // Trouver les chefs de service du service de l'agent
            // On cherche les utilisateurs du service ayant le rôle 'chef_service'
            $chefsService = User::where('service_id', $visite->user->service_id)
                ->role('chef_service')
                ->get();
                
            foreach ($chefsService as $chef) {
                $chef->notify(new VisiteMedicaleDepassee($visite));
            }
        }
        
        $this->info("{$count} visites médicales marquées comme dépassées et notifications envoyées.");
    }
}
