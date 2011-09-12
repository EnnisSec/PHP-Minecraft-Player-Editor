<?
require("PHP-NBT-Decoder-Encoder/nbt.class.php");

class MinecraftPlayerNBT extends NBT {

	public $properties = array();
	public $debug = 0;

	public function __construct($filename='') {
		if(is_file($filename)) {
			NBT::loadFile($filename);
			$this->properties = &$this->root[0]['value'];
		}
	}

	public function getProperty($property) {
		if($this->debug > 0) echo "Looking for '$property'...\n";
		foreach($this->properties as &$leaf) {
			$buf=$this->traverseReadProperties(&$leaf,$property);
			if($buf !== false) return($buf);
		}

	}	
	
	protected function traverseReadProperties($leaf,$property) {
		if($this->debug > 1) echo "traverseProperties: " . (isset($leaf['name']) ? $leaf['name'] : '<unnamed>') . " type ".$leaf['type']." value is ".$leaf['value']."\n";

		if(!isset($leaf['value'])) return(false); // can't match if there is no value
		elseif($leaf['type'] == 9 || $leaf['type'] == 10) return($this->traverseReadProperties($leaf['value'],$property));   // tree goes further down
		elseif(isset($leaf['name']) && $leaf['name'] == $property) return($leaf['value']);  // matched
		else return(false);  // no match
	}

	public function setProperty($property,$value) {
		if($this->debug > 0) echo "Attempting to set '$property' to '$value'...\n";
		foreach($this->properties as &$leaf) {
			$buf=$this->traverseWriteProperties(&$leaf,$property,$value);
			if($buf !== false) return($buf);
		}
	}

	protected function traverseWriteProperties($leaf,$property,$value) {
		if($this->debug > 1) echo "traverseProperties: " . (isset($leaf['name']) ? $leaf['name'] : '<unnamed>') . " type ".$leaf['type']." value is ".$leaf['value']."\n";

		if(!isset($leaf['value'])) return(false); // can't match if there is no value
		elseif($leaf['type'] == 9 || $leaf['type'] == 10) return($this->traverseReadProperties(&$leaf['value'],$property,$value));   // tree goes further down
		elseif(isset($leaf['name']) && $leaf['name'] == $property) return($leaf['value'] = $value);  // matched
		else return(false);  // no match
	}
}
?>
