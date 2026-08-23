<?php

namespace App\Console\Commands;

use App\Models\DeclarationJour;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('declarations:verrouiller')]
#[Description("Verrouille les déclarations dont l'heure limite de saisie (09h00) est dépassée et qui ne bénéficient pas d'une dérogation.")]
class VerrouillerDeclarations extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $aVerrouiller = DeclarationJour::query()
            ->where('statut', 'en_saisie')
            ->where('deroge', false)
            ->get()
            ->filter(fn (DeclarationJour $declaration) => now()->gte(
                Carbon::parse($declaration->date_repas->toDateString().' '.$declaration->heure_limite)
            ));

        foreach ($aVerrouiller as $declaration) {
            $declaration->update(['statut' => 'verrouillee']);
        }

        $this->info($aVerrouiller->count().' déclaration(s) verrouillée(s).');

        return self::SUCCESS;
    }
}
