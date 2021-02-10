<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Fach;
use App\Schwerpunkt;
use App\Auswahl;
use App\Stufe;

class KurswahlenController extends Controller
{
    public function index()
    {
    	return view('index');
    }

    public function zeigeSchwerpunkte()
    {
        $stufe = Stufe::find('sp');
    	$schwerpunkte = Schwerpunkt::all();

     	return view($stufe->code, compact('schwerpunkte', 'stufe'));  
    }    

    public function verarbeiteSchwerpunkte(Request $request)
    {
        $request->validate(['schwerpunkt' => 'required']);

    	$schwerpunkt = Schwerpunkt::where('code', $request->schwerpunkt)->first();
    	
    	$auswahl = new Auswahl(null);
    	$auswahl->addSchwerpunkt($schwerpunkt);
		$request->session()->put('auswahl', $auswahl);

    	return redirect('p1');
    }

    public function zeigeP1(Request $request)
    {
        $stufe = Stufe::find('p1');

    	$auswahl = $request->session()->get('auswahl');

	   	$schwerpunkt = $auswahl->schwerpunkt;
        $matrix      = $auswahl->matrix;
        $kernfaecher = $matrix->pluck('fach')->where('kf', '!=', NULL)->unique('kf');

        for( $i=0; $i<3; $i++ )
        {
            $lernfelder[$i] = $matrix->pluck('fach')->where('lf', $i)->count();
        }

    	switch($schwerpunkt->code) 
    	{
    		case "sp" : 
                $optionen = Fach::findMany(['EN','FR','LA','SN']);
    			break;

			case "mk" :
                $optionen = Fach::findMany(['KU','MU']); 
    			break;
    		
  			case "gw" : 
                $optionen = Fach::findMany(['GE']);
    			break;

    		case "nw" : 
                $optionen = Fach::findMany(['MA','BI','CH','PH']);
    			break;
    	}

    	return view('wahl', compact('optionen', 'schwerpunkt', 'matrix', 'lernfelder', 'kernfaecher', 'stufe'));  
    }

    public function verarbeiteP1(Request $request)
    {
        if($request->btn == "Zurück")
        {
            $auswahl = $request->session()->get('auswahl');
            $auswahl->matrix->pop();
            return redirect('schwerpunkt');
        }

        $request->validate(['p1' => 'required']);

        $stufe = Stufe::find('p1');
        $fach  = Fach::find($request->p1);
    	
    	if ($request->session()->has('auswahl'))
    	{
    		$auswahl = $request->session()->get('auswahl');
    		$auswahl->addFach($stufe, $fach);
			$request->session()->put('auswahl', $auswahl);
    	}

    	return redirect('p2');
    }
 
    public function zeigeP2(Request $request)
    {
        $stufe = Stufe::find('p2');
    
    	$auswahl = $request->session()->get('auswahl');

	   	$schwerpunkt  = $auswahl->schwerpunkt;
        $matrix       = $auswahl->matrix;
        $kernfaecher  = $matrix->pluck('fach')->where('kf', '!=', NULL)->unique('kf');

        for( $i=0; $i<3; $i++ )
        {
            $lernfelder[$i] = $matrix->pluck('fach')->where('lf', $i)->count();
        }

    	switch($schwerpunkt->code) 
    	{
    		case "sp" : 
    			$optionen = Fach::findMany(['EN','FR','LA','SN','DE']);
    			break;

			case "mk" :
				$optionen = Fach::findMany(["DE","MA"]); 
    			break;
    		
  			case "gw" : 
				$optionen = Fach::findMany(["DE","EN","FR","SN","LA","MA","BI","CH","PH"]); 
    			break;

    		case "nw" : 
				$optionen = Fach::findMany(["MA","BI","CH","PH","IF"]); 
    			break;
    	}

        // bereits gewählte Fächer herausfiltern
        $optionen = $optionen->diff($matrix->pluck('fach'));

        return view('wahl', compact('optionen', 'schwerpunkt', 'matrix', 'lernfelder', 'kernfaecher', 'stufe'));  
    }

    public function verarbeiteP2(Request $request)
    {
        if($request->btn == "Zurück")
        {
            $auswahl = $request->session()->get('auswahl');
            $fach    = $auswahl->matrix->pop();
            return redirect($fach->get('typ'));
        }   

        $request->validate(['p2' => 'required']);

        $fach  = Fach::find($request->p2);
        $stufe = Stufe::find('p2');

    	if ($request->session()->has('auswahl'))
        {
            $auswahl = $request->session()->get('auswahl');
            $auswahl->addFach($stufe, $fach);
            $request->session()->put('auswahl', $auswahl);
        }
		
    	return redirect('p3');
    }

	public function zeigeP3(Request $request)
    {
        $stufe = Stufe::find('p3');

    	$auswahl = $request->session()->get('auswahl');

	   	$schwerpunkt = $auswahl->schwerpunkt;
        $matrix      = $auswahl->matrix;
        $kernfaecher = $matrix->pluck('fach')->where('kf', '!=', NULL)->unique('kf');

        for( $i=0; $i<3; $i++ )
        {
            $lernfelder[$i] = $matrix->pluck('fach')->where('lf', $i)->count();
        }
    	
    	switch($schwerpunkt->code) 
    	{
    		case "gw" : 
                $optionen = Fach::findMany(['PW','RK','EK']);
    			break;

    		case "sp" : case "mk" : case "nw" :
                $optionen = Fach::findMany([
                    'DE','EN','FR','LA','SN','KU','MU','PW','GE',
                    'EK','RK','MA','BI','CH','PH','IF'
                ]);
    			break;
    	}

    	// bereits gewählte Fächer herausfiltern
        $optionen = $optionen->diff($matrix->pluck('fach'));

        $warnung = array();
    	// noch kein B-Fach und kein Kernfach gewählt?
    	if($lernfelder[1] == 0 && $kernfaecher->count() == 0) 
    	{
            $optionen = $optionen->filter(function ($fach) {
                return $fach->lf == 1 || $fach->kf != null;
            });

            $warnung["bk"] = "Filter aktiv: Es muss ein B-Fach oder ein Kernfach gewählt werden.";
    	}
    
    	return view('wahl', compact('optionen', 'schwerpunkt', 'lernfelder', 'kernfaecher', 'matrix', 'stufe', 'warnung'));  
	}

	public function verarbeiteP3(Request $request)
    {
        if($request->btn == "Zurück")
        {
            $auswahl = $request->session()->get('auswahl');
            $fach    = $auswahl->matrix->pop();
            return redirect($fach->get('typ'));
        }  

        $request->validate(['p3' => 'required']);

        $fach  = Fach::find($request->p3);
        $stufe = Stufe::find('p3');
  
    	if ($request->session()->has('auswahl'))
        {
            $auswahl = $request->session()->get('auswahl');
            $auswahl->addFach($stufe, $fach);
            $request->session()->put('auswahl', $auswahl);
        }
	
    	return redirect('p4');
    }

    public function zeigeP4(Request $request)
    {
        $stufe = Stufe::find('p4');

    	$auswahl = $request->session()->get('auswahl');

	   	$schwerpunkt = $auswahl->schwerpunkt;
        $matrix      = $auswahl->matrix;
        $kernfaecher = $matrix->pluck('fach')->where('kf', '!=', NULL)->unique('kf');

        for( $i=0; $i<3; $i++ )
        {
            $lernfelder[$i] = $matrix->pluck('fach')->where('lf', $i)->count();
        }

        $optionen = Fach::findMany([
            'DE','EN','FR','LA','SN','PW','GE','EK',
            'RK','RE','MA','BI','CH','PH','IF'
        ]);

        // alle bereits gewählte Fächer herausfiltern
        $optionen = $optionen->diff($matrix->pluck('fach'));

    	// wenn Kath. Religion schon gewählt, muss Ev. Religion raus
        if($matrix->pluck('fach')->contains('code', 'RK'))
    	{
            $optionen = $optionen->keyBy('code')->forget('RE');
    	}
		
        $warnung = array();

		// wenn kein Kernfach gewählt, müssen allle Nicht-Kernfächer raus
        if( $kernfaecher->count() == 0  )
		{
            $optionen = $optionen->filter(function ($fach) {
                return $fach->kf != null;
            });

    		$warnung["kf"] = "Filter aktiv: Es müssen noch 2 Kernfächer gewählt werden.";
    	}

        // wenn genau EIN Kernfach gewählt und noch KEIN B-Fach, 
        // muss ein Kernfach oder ein B-Fach gewählt werden.
        if( $kernfaecher->count() == 1 && $lernfelder[1] == 0 ) 
        {
            $optionen = $optionen->filter(function ($fach) {
                return $fach->lf == 1 || $fach->kf != null;
            });

            $warnung["bk"] = "Filter aktiv: Es muss ein B-Fach oder ein Kernfach gewählt werden.";

            // wenn das erste KF eine FS ist, müssen ALLE Fremdsprachen raus
            if( $kernfaecher->contains('kf', 'FS') )
            {
                $optionen = $optionen->filter(function ($fach) {
                    return $fach->fs == 0;
                });
            }
        }

        // wenn ein Lernfeld 3-mal angewählt worden ist, muss es raus
        for( $i=0; $i<3; $i++ ) 
        {
            if( $lernfelder[$i] == 3 ) 
            {    
                $optionen = $optionen->filter(function ($fach) use ($i) {
                    return $fach->lf != $i;
                });

                $warnung["lf"] = "Filter aktiv: Lernfeld ". chr(65+$i) . " ist 3x angewählt worden.";
            }
        }
        
    	return view('wahl', compact('optionen', 'schwerpunkt', 'lernfelder', 'kernfaecher', 'matrix', 'stufe', 'warnung'));  
    }

    public function verarbeiteP4(Request $request)
    {
        if($request->btn == "Zurück")
        {
            $auswahl = $request->session()->get('auswahl');
            $fach    = $auswahl->matrix->pop();
            return redirect($fach->get('typ'));
        }  

        $request->validate(['p4' => 'required']);

        $fach  = Fach::find($request->p4);
        $stufe = Stufe::find('p4');

    	if($request->session()->has('auswahl'))
        {
            $auswahl = $request->session()->get('auswahl');
            $auswahl->addFach($stufe, $fach);
            $request->session()->put('auswahl', $auswahl);
        }
    	
    	return redirect('p5');
    }

    public function zeigeP5(Request $request)
    {
        $stufe = Stufe::find('p5');

    	$auswahl = $request->session()->get('auswahl');

	   	$schwerpunkt = $auswahl->schwerpunkt;
        $matrix      = $auswahl->matrix;
        $kernfaecher = $matrix->pluck('fach')->where('kf', '!=', NULL)->unique('kf');

        for( $i=0; $i<3; $i++ )
        {
            $lernfelder[$i] = $matrix->pluck('fach')->where('lf', $i)->count();
        }

        $optionen = Fach::findMany([
            'DE','EN','FR','LA','SN','MU','PW','GE','EK',
            'RK','RE','MA','BI','CH','PH','IF','PL'
        ]);

        $warnung = array();

    	if( $lernfelder[0] == 0 )
    	{
            $optionen = Fach::findMany(['DE','EN','FR','LA','SN','MU']);
	    	$warnung["lf"] = "Filter aktiv: Fach aus Lernfeld A muss noch gewählt werden.";
    	}
    	elseif( $lernfelder[1] == 0 )
    	{
            $optionen = Fach::findMany(['PW','GE','EK','RK','RE','PL']);
	    	$warnung["lf"] = "Filter aktiv: Fach aus Lernfeld B muss noch gewählt werden.";
    	}
    	elseif( $lernfelder[2] == 0 )
    	{
            $optionen = Fach::findMany(['MA','BI','CH','PH','IF']);
			$warnung["lf"] = "Filter aktiv: Fach aus Lernfeld C muss noch gewählt werden.";
    	}
        else
        {
            // alle Lernfelder sind abgedeckt.
            // Sport P5 möglich, wenn auch 2 Kernfächer schon gewählt
            if( $kernfaecher->count() > 1 ) 
            {
                $optionen->push(Fach::find('SP'));
            }
        }

        // alle bereits gewählte Fächer herausfiltern
        $optionen = $optionen->diff($matrix->pluck('fach'));

        // wenn Kath./Ev. Religion schon gewählt, muss Ev./Kath. Religion raus
        if( $matrix->pluck('fach')->contains('code', 'RK')
            || $matrix->pluck('fach')->contains('code', 'RE') )
        {
            $optionen = $optionen->keyBy('code')->forget(['RK','RE']);
        }

        // wenn Schwerpunkt GW, dann kein Erdkunde P5
        if($schwerpunkt->code == 'gw' && !$matrix->pluck('fach')->contains('code', 'EK')) 
        {
            $optionen = $optionen->keyBy('code')->forget('EK');

            $warnung["ek"] = "Filter aktiv: Erdkunde kann nicht gewählt werden.";
        }

        // wenn Schwerpunkt MK, dann kein Musik P5
        /*
        if($schwerpunkt->code == 'mk' && !$matrix->pluck('fach')->contains('code', 'MU')) 
        {
            $optionen = $optionen->keyBy('code')->forget('MU');

            $warnung["mu"] = "Filter aktiv: Musik kann nicht gewählt werden.";
        }
        */

    	// wenn ein Kernfach fehlt, dann alles raus, was kein Kernfach ist
        if( $kernfaecher->count() == 1 )
        {
            $optionen = $optionen->filter(function ($fach) {
                return $fach->kf != null;
            });

            // wenn das erste KF eine FS ist, müssen ALLE Fremdsprachen raus
            if( $kernfaecher->contains('kf', 'FS') )
            {
                $optionen = $optionen->filter(function ($fach) {
                    return $fach->fs == 0;
                });
            }

            $warnung["kf"] = "Filter aktiv: Es muss noch 1 Kernfach gewählt werden.";
            unset($warnung["mu"]);
            unset($warnung["ek"]);
        }
	   
    	return view('wahl', compact('optionen', 'schwerpunkt', 'lernfelder', 'warnung', 'kernfaecher', 'matrix', 'stufe'));  
    }

    public function verarbeiteP5(Request $request)
    {
        if($request->btn == "Zurück")
        {
            $auswahl = $request->session()->get('auswahl');
            $fach    = $auswahl->matrix->pop();
            return redirect($fach->get('typ'));
        }  

        $request->validate(['p5' => 'required']);

        $fach  = Fach::find($request->p5);
        $stufe = Stufe::find('p5');
    	
    	if ($request->session()->has('auswahl'))
        {
            $auswahl = $request->session()->get('auswahl');
            if($fach->code == 'SP')
            {
                $auswahl->addSportP5($stufe->code, $fach, 4, 4, 4);    
            }
            else
            {
                $auswahl->addFach($stufe, $fach);
            }
            $request->session()->put('auswahl', $auswahl);
        }

    	return redirect('k1');
    }

    public function verarbeiteK1(Request $request)
    {
        if ($request->session()->has('auswahl'))
        {
            $auswahl     = $request->session()->get('auswahl');
            $schwerpunkt = $auswahl->schwerpunkt;
            $matrix      = $auswahl->matrix;

            $stufe = Stufe::find('k1');
            $fach  = Fach::find('DE');
            
            // Deutsch ergänzen
            if( !$matrix->pluck('fach')->contains('code', 'DE') )
            {
                $auswahl->addFach($stufe, $fach);
                $warnung["k1"] = "Fach Deutsch ergänzt.";
            }        

            $request->session()->put('auswahl', $auswahl);
        }

        return redirect('k2');
    }

    public function zeigeK2(Request $request)      
    {
        $stufe = Stufe::find('k2');

        $auswahl = $request->session()->get('auswahl');

        $schwerpunkt = $auswahl->schwerpunkt;
        $matrix      = $auswahl->matrix;
        $kernfaecher = $matrix->pluck('fach')->where('kf', '!=', NULL)->unique('kf');

        for( $i=0; $i<3; $i++ )
        {
            $lernfelder[$i] = $matrix->pluck('fach')->where('lf', $i)->count();
        }     

        // wenn FS gewählt UND (nicht sprachlich ODER nicht DE ODER mind 2 FS)
        if( $kernfaecher->contains('kf', 'FS') 
            && ( $auswahl->schwerpunkt->code != "sp" 
                || !$kernfaecher->contains('kf', 'DE')
                || $auswahl->matrix->pluck('fach')->where('kf', 'FS')->count() > 1 )
        )
        {
            return redirect('k3');
        }
        
        $optionen = Fach::findMany(['EN','FR','LA','SN']);

        // alle bereits gewählte Fächer herausfiltern
        $optionen = $optionen->diff($matrix->pluck('fach'));
        
        return view('wahl', compact('optionen', 'schwerpunkt', 'lernfelder', 'kernfaecher', 'matrix', 'stufe')); 
    }


    public function verarbeiteK2(Request $request)
    {
        if($request->btn == "Zurück")
        {
            $auswahl = $request->session()->get('auswahl');
            $fach    = $auswahl->matrix->pop();
            return redirect($fach->get('typ'));
        }  

        $request->validate(['k2' => 'required']);

        $fach  = Fach::find($request->k2);
        $stufe = Stufe::find('k2');

        if ($request->session()->has('auswahl'))
        {
            $auswahl = $request->session()->get('auswahl');
            $auswahl->addFach($stufe, $fach);
            $request->session()->put('auswahl', $auswahl);
        }

        return redirect('k3');
    }

    public function verarbeiteK3(Request $request)
    {
        if ($request->session()->has('auswahl'))
        {
            $auswahl     = $request->session()->get('auswahl');
            $schwerpunkt = $auswahl->schwerpunkt;
            $matrix      = $auswahl->matrix;

            $stufe = Stufe::find('k3');
            $fach  = Fach::find('MA');

            // Mathematik ergänzen?
            if( !$matrix->pluck('fach')->contains('code', 'MA') )
            {
                $auswahl->addFach($stufe, $fach);
                $warnung["k3"] = "Fach Mathematik ergänzt.";
            }  

            $request->session()->put('auswahl', $auswahl);
        }

        return redirect('e1');
    }

    public function zeigeE1(Request $request)	// Naturwissenschaft
    {
        $stufe = Stufe::find('e1');

	    $auswahl = $request->session()->get('auswahl');

	   	$schwerpunkt = $auswahl->schwerpunkt;
        $matrix      = $auswahl->matrix;

        for( $i=0; $i<3; $i++ )
        {
            $lernfelder[$i] = $matrix->pluck('fach')->where('lf', $i)->count();
        }

        // wenn noch keine NW gewählt...
	   	if( $matrix->pluck('fach')->where('nw')->count() == 0
            // im NW-Schwerpunkt muss eine zusätzliche NW oder IF gewählt werden
            || ( $schwerpunkt->code == "nw" 
                && $matrix->pluck('fach')->where('nw')->count() == 1 
                && !$matrix->pluck('fach')->contains('code', 'IF')
            )
        )
	   	{
            $optionen = Fach::findMany(['BI','CH','PH','IF']);

			if($schwerpunkt->code != "nw")
			{
                $optionen = $optionen->keyBy('code')->forget('IF');
			}

            // alle bereits gewählte Fächer herausfiltern
            $optionen = $optionen->diff($matrix->pluck('fach'));

    		return view('wahl', compact('optionen', 'schwerpunkt', 'lernfelder', 'matrix', 'stufe')); 
	   	}
	   	else
	   	{
	   		return redirect('e2');
	   	}
    }

    public function verarbeiteE1(Request $request)
    {
        if($request->btn == "Zurück")
        {
            $auswahl = $request->session()->get('auswahl');
            $fach    = $auswahl->matrix->pop();
            return redirect($fach->get('typ'));
        }  

        $request->validate(['e1' => 'required']);

        $stufe = Stufe::find('e1');
        $fach  = Fach::find($request->e1);
        
        if ($request->session()->has('auswahl'))
        {
            $auswahl = $request->session()->get('auswahl');
            $auswahl->addFach($stufe, $fach);
            $request->session()->put('auswahl', $auswahl);
        }

    	return redirect('e2');
    }

    public function zeigeE2(Request $request)	// Religion
    {
        $stufe = Stufe::find('e2');

	    $auswahl = $request->session()->get('auswahl');

	   	$schwerpunkt = $auswahl->schwerpunkt;
        $matrix      = $auswahl->matrix;

        for( $i=0; $i<3; $i++ )
        {
            $lernfelder[$i] = $matrix->pluck('fach')->where('lf', $i)->count();
        }

        if( $matrix->pluck('fach')->contains('code', 'RK')
            || $matrix->pluck('fach')->contains('code', 'RE') )
	   	{
            return redirect('e3');        
	   	}
    	
        $optionen = Fach::findMany(['RE','RK']);

        $wahl = 'e2';

        return view('wahl', compact('optionen', 'schwerpunkt', 'lernfelder', 'matrix', 'stufe'));
    }

    public function verarbeiteE2(Request $request)
    {
        if($request->btn == "Zurück")
        {
            $auswahl = $request->session()->get('auswahl');
            $fach    = $auswahl->matrix->pop();
            return redirect($fach->get('typ'));
        }  

        $request->validate(['e2' => 'required']);

        $stufe = Stufe::find('e2');
        $fach  = Fach::find($request->e2);

        if ($request->session()->has('auswahl'))
        {
            $auswahl = $request->session()->get('auswahl');
            $auswahl->addFach($stufe, $fach);
            $request->session()->put('auswahl', $auswahl);
        }

    	return redirect('e3');
    }

    public function zeigeE3(Request $request)	// Kunst oder Musik
    {
        $stufe = Stufe::find('e3');

	    $auswahl = $request->session()->get('auswahl');

	   	$schwerpunkt = $auswahl->schwerpunkt;
        $matrix      = $auswahl->matrix;

        for( $i=0; $i<3; $i++ )
        {
            $lernfelder[$i] = $matrix->pluck('fach')->where('lf', $i)->count();
        }

        if( ($schwerpunkt->code != 'mk' &&
            ( $matrix->pluck('fach')->contains('code', 'KU')
                || $matrix->pluck('fach')->contains('code', 'MU') 
            )) ||
            ($schwerpunkt->code == 'mk' &&
            ( $matrix->pluck('fach')->contains('code', 'KU')
                && $matrix->pluck('fach')->contains('code', 'MU') 
            )) 
        )
        {
            return redirect('e4');
        }
        
        $optionen = Fach::findMany(['MU','KU']);
        
        // alle bereits gewählte Fächer herausfiltern
        $optionen = $optionen->diff($matrix->pluck('fach'));
        
        return view('wahl', compact('optionen', 'schwerpunkt', 'lernfelder', 'matrix', 'stufe'));
    }

    public function verarbeiteE3(Request $request)
    {
        if($request->btn == "Zurück")
        {
            $auswahl = $request->session()->get('auswahl');
            $fach    = $auswahl->matrix->pop();
            return redirect($fach->get('typ'));
        }  

        $request->validate(['e3' => 'required']);

        $stufe = Stufe::find('e3');
        $fach  = Fach::find($request->e3);

    	if ($request->session()->has('auswahl'))
        {
            $auswahl = $request->session()->get('auswahl');
            $auswahl->addFach($stufe, $fach);
            $request->session()->put('auswahl', $auswahl);
        }

    	return redirect('e4');
    }

    public function zeigeE4(Request $request)	// weitere NW, FS oder IF im GW-Schwerpunkt
    {
        $stufe = Stufe::find('e4');

	    $auswahl = $request->session()->get('auswahl');

	   	$schwerpunkt = $auswahl->schwerpunkt;
        $matrix      = $auswahl->matrix;

        for( $i=0; $i<3; $i++ )
        {
            $lernfelder[$i] = $matrix->pluck('fach')->where('lf', $i)->count();
        }

	   	// wenn NICHT Schwerpunkt GW, dann weiter
   		if($schwerpunkt->code != "gw") 
        {
	    	return redirect('e5678');
   		} 

		// wenn 2 Fremdsprachen ODER 2 Naturwissenschaften, dann weiter
		if( $matrix->pluck('fach')->where('fs')->count() > 1 
            || $matrix->pluck('fach')->where('nw')->count() > 1 ) {
	    	return redirect('e5678');
		}
	
		// wenn Informatik gewählt, dann weiter
        if( $matrix->pluck('fach')->contains('code', 'IF') )
        {
	    	return redirect('e5678');			
		} 

        $optionen = Fach::findMany(['EN','FR','LA','SN','BI','CH','PH','IF']);

        // alle bereits gewählte Fächer herausfiltern
        $optionen = $optionen->diff($matrix->pluck('fach'));

	    return view('wahl', compact('optionen', 'schwerpunkt', 'lernfelder', 'matrix', 'stufe'));
    }

    public function verarbeiteE4(Request $request)
    {
        if($request->btn == "Zurück")
        {
            $auswahl = $request->session()->get('auswahl');
            $fach    = $auswahl->matrix->pop();
            return redirect($fach->get('typ'));
        }  

        $request->validate(['e4' => 'required']);

        $stufe = Stufe::find('e4');
        $fach  = Fach::find($request->e4);

    	if ($request->session()->has('auswahl'))
        {
            $auswahl = $request->session()->get('auswahl');
            $auswahl->addFach($stufe, $fach);
            $request->session()->put('auswahl', $auswahl);
        }

    	return redirect('e5678');
    }

    public function verarbeiteE5678(Request $request)
    {	
    	if ($request->session()->has('auswahl'))
    	{
    		$auswahl     = $request->session()->get('auswahl');
    		$schwerpunkt = $auswahl->schwerpunkt;
            $matrix      = $auswahl->matrix;

            // Geschichte ergänzen (E5)
            $stufe = Stufe::find('e5');

            if( !$matrix->pluck('fach')->contains('code', 'GE') )
 	        {
				$auswahl->addFach($stufe, Fach::find('GE'));
                $warnung["e5"] = "Geschichte ergänzt.";
			} 		

            // Politik-Wirtschaft ergänzen (E6)
            $stufe = Stufe::find('e6');

            if( !$matrix->pluck('fach')->contains('code', 'PW') )
            {
                // GW Schwerpunkt mit P3 Erdkunde?
                if(! ($schwerpunkt->code =="gw" &&
                    $matrix->where('typ','p3')->first()["fach"]->code == 'EK') )
                {
                    $auswahl->addFach($stufe, Fach::find('PW'));
                    $warnung["e6"] = "Politik-Wirtschaft ergänzt.";
                }
            } 
	  		
			// Sport ergänzen (E7)
            $stufe = Stufe::find('e7');

            if( !$matrix->pluck('fach')->contains('code', 'SP') )
            {
    			$auswahl->addFach($stufe, Fach::find('SP'));
                $warnung["e7"] = "Sport ergänzt.";
            }

			// Seminarfach ergänzen (E8)
            $stufe = Stufe::find('e8');

            $auswahl->addFach($stufe, Fach::find('SF'));
            $warnung["e8"] = "Seminarfach ergänzt.";
		
			$request->session()->put('auswahl', $auswahl);
    	}

    	return redirect('wf');
    }

    public function zeigeWahlfach(Request $request)
    {
        $stufe = Stufe::find('wf');

	    $auswahl = $request->session()->get('auswahl');

	   	$schwerpunkt = $auswahl->schwerpunkt;
        $matrix      = $auswahl->matrix;

        for( $i=0; $i<3; $i++ )
        {
            $lernfelder[$i] = $matrix->pluck('fach')->where('lf', $i)->count();
        }

        $summe1 = $matrix->pluck('einbringung')->sum();

        $summe2 = 0;
        foreach($matrix as $w)
        {   
            $summe2 += $w["stunden"] * $w["halbjahre"];
        }

        $optionen = Fach::all();

         // alle bereits gewählte Fächer herausfiltern
        $optionen = $optionen->diff($matrix->pluck('fach'));

        // wenn Kath./Ev. Religion schon gewählt, muss Ev./Kath. Religion raus
        if( $matrix->pluck('fach')->contains('code', 'RK')
            || $matrix->pluck('fach')->contains('code', 'RE') )
        {
            $optionen = $optionen->filter(function ($fach) {
                return $fach->code != 'RE' && $fach->code != 'RK';
            });
        }

	   	return view('wahlfach', compact('optionen', 'schwerpunkt', 'lernfelder', 'matrix', 'summe1', 'summe2', 'stufe')); 
    }

    public function verarbeiteWahlfach(Request $request)
    {
        $fach  = Fach::find($request->wahlfach);
        $stufe = Stufe::find('wf');

        if ($request->session()->has('auswahl'))
        {
            $auswahl = $request->session()->get('auswahl');
            $auswahl->addFach($stufe, $fach);
            $request->session()->put('auswahl', $auswahl);
        }

        return redirect('wf');
    }

    public function loescheWahlfach(Request $request)
    {
        $fach = Fach::find($request->wahlfach);

        if ($request->session()->has('auswahl'))
        {
            $auswahl = $request->session()->get('auswahl');
            $auswahl->removeFach($fach);
            $request->session()->put('auswahl', $auswahl);
        }

        return redirect('wf');
    } 

    public function aendereHalbjahre(Request $request, $code)
    {
        $std    = $request->halbjahre;
        $fach   = Fach::find($code);

        if ($request->session()->has('auswahl'))
        {
            $auswahl = $request->session()->get('auswahl');            
            $auswahl->updateHalbjahre($fach, $std);
            $request->session()->put('auswahl', $auswahl);            
        }

        return redirect('wf');
    }
}