<?php

namespace Database\Seeders\Concerns;

use Illuminate\Database\Eloquent\Model;

trait ConFechaHistorica
{
    /**
     * Overrides created_at/updated_at on an already-saved model, since
     * Eloquent always stamps "now" on create() otherwise. Used so seeded
     * data reads as a year-plus of real activity instead of everything
     * dated the moment the seeder ran.
     */
    private function conFecha(Model $modelo, \DateTimeInterface $fecha): Model
    {
        $modelo->timestamps = false;
        $modelo->created_at = $fecha;
        $modelo->updated_at = $fecha;
        $modelo->save();

        return $modelo;
    }
}
