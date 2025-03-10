<?php
class Calendar {
    public $eventos = [];
    public $selecc = false;
    private $active_year, $active_month, $active_day;
    private $idestacion;
    private $mesletra = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

    public function __construct($date = null,$idestacion) {
        $this->active_year = $date != null ? date('Y', strtotime($date)) : date('Y');
        $this->active_month = $date != null ? date('m', strtotime($date)) : date('m');
        $this->active_day = $date != null ? date('d', strtotime($date)) : date('d');
        $this->idestacion = $idestacion;
    }
/*
    public function __toString() {
        $num_days = date('t', strtotime($this->active_day . '-' . $this->active_month . '-' . $this->active_year));
        $num_days_last_month = date('j', strtotime('last day of previous month', strtotime($this->active_day . '-' . $this->active_month . '-' . $this->active_year)));
        $days = [0 => 'D', 1 => 'L', 2 => 'M', 3 => 'M', 4 => 'J', 5 => 'V', 6 => 'S'];
        $first_day_of_week = date('N', strtotime($this->active_year . '-' . $this->active_month . '-1'));
        $html = '<div class="row"><div class="col-12"><div class="calendar">';
        $html .= '<div class="header">';
        $html .= '<div class="month-year">';
        // agregar mes anterior
        $html .= $this->mesletra[date('n', strtotime($this->active_year . '-' . $this->active_month . '-' . $this->active_day))-1] . " - " . $this->active_year;
        // agregar mes sig
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="days">';
        foreach ($days as $day) {
            $html .= '<div class="day_name">' . $day . '</div>';
        }
        for ($i = $first_day_of_week; $i > 0; $i--) {
            $html .= '<div class="day_num ignore"><span>&nbsp;&nbsp;&nbsp;' . ($num_days_last_month-$i+1) . '</span></div>';
        }
        for ($i = 1; $i <= $num_days; $i++) {
            $selected = '';
            if ($i == $this->active_day) {
                $selected = ' selected';
            } elseif ($i < $this->active_day) {
                $selected = ' ignore';
            }
            $html .= '<div class="day_num' . $selected . '">';
            $html .= '<span>&nbsp;&nbsp;&nbsp;' . $i . '</span>';
            foreach ($this->events as $event) {
                for ($d = 0; $d <= ($event[2]-1); $d++) {
                    if (date('y-m-d', strtotime($this->active_year . '-' . $this->active_month . '-' . $i . ' -' . $d . ' day')) == date('y-m-d', strtotime($event[1]))) {
                        $html .= '<div class="event' . $event[3] . '">';
                        $html .= $event[0];
                        $html .= '</div>';
                    }
                }
            }
            $html .= '</div>';
        }
        for ($i = 1; $i <= (42-$num_days-max($first_day_of_week, 0)); $i++) {
            $html .= '<div class="day_num"><span>&nbsp;&nbsp;&nbsp;' . $i . '</span></div>';
        }
        $html .= '</div></div></div></div>';
        return $html;
    }
*/
    public function __toString(){
        $num_days = date('t', strtotime($this->active_day . '-' . $this->active_month . '-' . $this->active_year));
        $num_days_last_month = date('j', strtotime('last day of previous month', strtotime($this->active_day . '-' . $this->active_month . '-' . $this->active_year)));
        $days = [0 => 'D', 1 => 'L', 2 => 'M', 3 => 'M', 4 => 'J', 5 => 'V', 6 => 'S'];
        $first_day_of_week = date('N', strtotime($this->active_year . '-' . $this->active_month . '-1'));
        $html = '<div class="col-12" id="divcalendario_'.$this->idestacion.'">';
        if ($this->active_month == date("m")){
            $html .= '<table id="calendario"><caption>' . $this->mesletra[date('n', strtotime($this->active_year . '-' . $this->active_month . '-' . $this->active_day))-1] . " - " . $this->active_year . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-primary" onclick="sigMesCasa(\''. base64_encode(date('Y-m-d', strtotime($this->active_year . '-' . $this->active_month . '-' . $this->active_day . ' +1 month'))).'\',\''.$this->idestacion.'\',\''.$this->selecc.'\');"> <i class="fa fa-angle-right"></i></button></caption><tr class="weekdays">';
        } else {
            $html .= '<table id="calendario"><caption><button class="btn btn-primary" onclick="sigMesCasa(\''. base64_encode(date('Y-m-d', strtotime($this->active_year . '-' . $this->active_month . '-' . $this->active_day . ' -1 month'))).'\',\''.$this->idestacion.'\',\''.$this->selecc.'\');"> <i class="fa fa-angle-left"></i></button> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $this->mesletra[date('n', strtotime($this->active_year . '-' . $this->active_month . '-' . $this->active_day))-1] . " - " . $this->active_year . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-primary" onclick="sigMesCasa(\''. base64_encode(date('Y-m-d', strtotime($this->active_year . '-' . $this->active_month . '-' . $this->active_day . ' +1 month'))).'\',\''.$this->idestacion.'\',\''.$this->selecc.'\');"> <i class="fa fa-angle-right"></i></button></caption><tr class="weekdays">';
        }
        foreach ($days as $day) {
            $html .= '<th>' . $day . '</th>';
        }
        $html .= '</tr><tr>';
        for ($i = $first_day_of_week; $i > 0; $i--) {
            $html .= '<td class="inactivo"><span>&nbsp;&nbsp;&nbsp;' . ($num_days_last_month-$i+1) . '</span></td>';
        }
        $col = $first_day_of_week;
        $linea = 1;
        $idevento = 0;
        $dias_evento = 0;
        $arrdoble = [];
        for ($i = 1; $i <= $num_days; $i++) {
            $base64Fecha = base64_encode(date('Y-m-d',strtotime($this->active_year . '-' . $this->active_month . '-' .  str_pad($i,2,"0",STR_PAD_LEFT) )));
            if ($col == 7) {
                $html .= '</tr><tr>';
                $linea ++;
                $col = 1;
            } else {
                $col ++;
            }
            $clase = 'activo';

            $encontrado = false;
            $ultimo = "";
            $ultimo_nombre = "";
            foreach ($this->eventos as $event) {
                for ($d = 0; $d <= ($event[2]-1); $d++) {
                    if (date('y-m-d', strtotime($this->active_year . '-' . $this->active_month . '-' . $i . ' -' . $d . ' day')) == date('y-m-d', strtotime($event[1]))) {
                        $clase = 'evento';
                        $dias_evento = $event[2];
                        $encontrado = true;
                        if ($idevento != $event[3]) {
                            $idevento = $event[3];
                            $clase .= ' evento-inicio';
                            if ($ultimo !== ""){
                                if ($ultimo == date('y-m-d',strtotime($this->active_year . '-' . $this->active_month . '-' . $i))){
                                    $clase .= ' evento-doble';
//                                    array_push($arrdoble,"(" . date('d-m-Y',strtotime($this->active_year . '-' . $this->active_month . '-' . $i)). ")<br>Sale:<label class='text-danger'>" . $ultimo_nombre . "</label> <br>Entra:<label class='text-success'>" . $event[0] . "</label>");
                                }
                            }
                        }
                        if ($d+1 == $dias_evento) {
                            $clase .= ' evento-fin';
                            $idevento = 0;
                            $dias_evento = 0;
                        }
                        $ultimo = date('y-m-d',strtotime($event[1] . ' +' . ($event[2]-1) . ' day'));
                        $ultimo_nombre = $event[0];
                        break;
                    }
                }
            }
            if (!$encontrado) {
                $idevento = 0;
                $dias_evento = 0;
            }

            if ($this->active_month == date("m")){
                if ($i < $this->active_day) {
                    $clase = 'inactivo';
                }
            }

            //if (($clase == "activo") && (!$this->selecc)) {
            //    $liga = 'ajaxpage(\'registro_casa.php?idestacion='.$this->idestacion.'\',\'contenido\');';
            //}
            $liga = "";
            $cursor_style = "";
            switch ($clase){
                case "activo":
                    if ($this->selecc) {
                        if ($i == $this->active_day){ $clase = "selCalendarioFecha"; }
                        $cursor_style = 'style="cursor: pointer;"';
                        $liga = 'selCalendarioDia(\''.$base64Fecha.'\');';
                    } else {
                        $liga = 'ajaxpage(\'registro_casa.php?idestacion='.$this->idestacion.'\',\'contenido\');';
                        $cursor_style = 'style="cursor: pointer;"';
                    }
                    break;
                case "evento":
                case "evento evento-fin":
                case "evento evento-inicio":
                case "evento evento-inicio evento-doble":
                    if (!$this->selecc) $cursor_style = 'style="cursor: pointer;"';
                    break;
            }

            //if ((!$this->selecc) && (($clase == "evento") || ($clase == "activo") || ($clase == "evento evento-fin") || ($clase == "evento evento-inicio evento-doble") || ($clase == "evento evento-inicio"))) { $cursor_style = 'style="cursor: pointer;"'; }
            //if (($this->selecc) && ($clase == "activo")) { $cursor_style = 'style="cursor: pointer;"'; }
            $html .= '<td class="'.$clase.'" id="'.base64_decode($base64Fecha).'" onclick="'.$liga.'" '.$cursor_style.'>';
            $html .= '<span>&nbsp;&nbsp;&nbsp;' . $i .'</span>';
            $html .= '</td>';
        }

        for ($i = 1; $i <= (42-$num_days-max($first_day_of_week, 0)); $i++) {
            $base64Fecha = base64_encode(date('Y-m-d',strtotime($this->active_year . '-' . $this->active_month . '-' . $num_days . ' +' . $i . ' day')));
            //$cursor_style = "";
            if ($col == 7) {
                $html .= '</tr><tr>';
                $col = 1;
                $linea ++;
            } else {
                $col ++;
            }
            $clase = "other-month";
            if ($idevento > 0){
                $clase = "evento";
                if ($d+$i == $dias_evento) {
                    $clase .= ' evento-fin';
                    $idevento = 0;
                }
            }

            $liga = 'sigMesCasa(\''. $base64Fecha.'\',\''.$this->idestacion.'\',\''.$this->selecc.'\');';
            $cursor_style = "";
            switch ($clase){
                case "other-month":
                    if ($this->selecc) {
                        $cursor_style = 'style="cursor: pointer;"';
                        //$liga = 'sigMesCasa(\''. $base64Fecha.'\',\''.$this->idestacion.'\',\''.$this->selecc.'\');';
                    } else {
                        $cursor_style = 'style="cursor: pointer;"';
                        //$liga = 'ajaxpage(\'registro_casa.php?idestacion='.$this->idestacion.'\',\'contenido\');';
                    }
                    break;
                case "evento":
                case "evento evento-fin":
                    if (!$this->selecc) $cursor_style = 'style="cursor: pointer;"';
                    break;
            }

            //if ((!$this->selecc) && (($clase == "other-month") || ($clase == "evento") || ($clase == "activo") || ($clase == "evento evento-fin") || ($clase == "evento evento-inicio evento-doble") || ($clase == "evento evento-inicio"))) { $cursor_style = 'style="cursor: pointer;"'; }
            //if (($this->selecc) && ($clase == "other-month")) { $cursor_style = 'style="cursor: pointer;"'; }
            $html .= '<td class="'.$clase.'" id="'.base64_decode($base64Fecha).'" onclick="'.$liga.'" '.$cursor_style.'><span>&nbsp;&nbsp;&nbsp;' . $i .'</span></td>';
        }
        $html .= '</tr></table><div class="col-12 text-left">';
        for ($i=0;$i<sizeof($arrdoble);$i++){
            $html .= '<br><label>'.$arrdoble[$i].'</label>';
        }
        $html .= '</div></div>';
        return $html;
    }
}
?>
