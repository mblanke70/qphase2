<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auswahl
{
    public function __construct($alteAuswahl)
    {
    	if($alteAuswahl)
    	{
    		$this->schwerpunkt = $alteAuswahl->schwerpunkt;
            $this->matrix      = $alteAuswahl->matrix;
    	} else {
    		$this->schwerpunkt = null;
            $this->matrix      = collect();
    	}
    }

    public function addSchwerpunkt($schwerpunkt)
    {
    	$this->schwerpunkt = $schwerpunkt;
    }

    public function addSportP5($typ, $fach, $stunden, $einbringung, $halbjahre)
    {   
        $collection = collect([
            "typ"         => $typ, 
            "fach"        => $fach,
            "stunden"     => $stunden,
            "einbringung" => $einbringung,
            "halbjahre"   => $halbjahre,
        ]);

        $this->matrix->push($collection);
    }

    public function addFach($stufe, $fach)
    {   
        $collection = collect([
            "typ"         => $stufe->code, 
            "fach"        => $fach,
            "stunden"     => $stufe->stunden,
            "einbringung" => $stufe->einbringung,
            "halbjahre"   => $stufe->halbjahre,
        ]);

        $this->matrix->push($collection);
    }

    public function removeFach($fach)
    {
    	$this->matrix = $this->matrix->keyBy('fach.code')->forget($fach->code);
    }

    public function updateHalbjahre($fach, $std)
    {
        $this->matrix->keyBy('fach.code')->get($fach->code)->put('halbjahre',$std);   
    }
}