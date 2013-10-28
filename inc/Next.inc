<?php

class next_issue {
	private $stamp=0;
	private $next=2;
	private $y;
	private $Ym;
	private $format;
	public $default=0;

	function __construct($stamp=0){
// <=20/1 => Feb/March
// <=20/3 => April/May
// <=20/5 => June/July
// <=20/7 => August/September
// <=20/9 => October/November
// <=20/11 => December/January
		if ($stamp==0)
			$stamp=time();
		$this->stamp=$stamp;
        	$test=(string) date('md',$this->stamp);

        	for ($i=1;$i<12;$i+=2){
                	$fmt= (string) sprintf('%02d',$i).'20';
                	if ($test <= $fmt){
                        	$this->next=$i+1;
				break;
			}
        	}
		$this->y=date('Y',$this->stamp);
		if ($this->next < date('m',$this->stamp)-2)
			$this->y++;
	}

	function option($year){
		if (is_float($year)){
			$bits=explode(".",$year);
			$max=($bits[0] * 12) -2 + $bits[1];
		}
		else
			$max = ($year * 12) -2;
		$out='';
		for ($i=0;$i<=$max;$i+=2){
			$out.=$this->core($i);
		}
		return $out;
	}

	function current(){
		$this->core(0);
		return $this->Ym;
	}	

	function format(){
		$this->core(0);
		return $this->format;
	}	
	
	private function core($i){
		$out='';
		$m=($this->next+$i) % 12;
		$Y=$this->y;
		if ($m==0){
			$m=12;
			$this->y++;
		}
		$opt=$Y.sprintf('%02d',($m%12 ==0 ? 12 : $m%12));
		$this->Ym=$opt;
		if ($m==12)
			$Y++;
		$s=date('F',mktime (0,0,0,$m,1)).'/'. date('F',mktime (0,0,0,($m+1)%12,1))." $Y";
		$this->format=$s;
		$sel='';
		if ($opt==$this->default)
			$sel='selected="selected" ';
		$out.='<option '.$sel.'value="'.$opt.'">'.$s.'</option>'.PHP_EOL;
		return $out;
	}
			
}

?>
