if (!function_exists("json_encode")) {
   function json_encode($var, $options=0, $_indent="") {

      // prepare JSON string
      $obj = ($options & JSON_FORCE_OBJECT);
      list($_space, $_tab, $_nl) = ($options & JSON_PRETTY_PRINT)
            ? array(" ", "    $_indent", "\n") : array("", "", "");
      $json = "$_indent";

      if ($options & JSON_NUMERIC_CHECK and is_string($var) and is_numeric($var)) {
          $var = (strpos($var, ".") || strpos($var, "e")) ? floatval($var) : intval($var);
      }

      // add array entries
      if (is_array($var) || ($obj=is_object($var))) {

         // check if array is associative
         if (!$obj) {
            $keys = array_keys((array)$var);
            
            // keys must be in 0,1,2,3, ordering, but PHP treats integers==strings otherwise
            $obj = !($keys == array_keys($keys));
         }

         // concat individual entries
         $empty = 0; $json = "";
         foreach ((array)$var as $i=>$v) {
            $json .= ($empty++ ? ",$_nl" : "")
                   . $_tab . ($obj ? (json_encode($i, $options, $_tab) . ":$_space") : "")
                   . (json_encode($v, $options, $_tab));
         }

         // enclose into braces or brackets
         $json = $obj ? "{"."$_nl$json$_nl$_indent}" : "[$_nl$json$_nl$_indent]";
      }

      // strings need some care
      elseif (is_string($var)) {

         if (!utf8_decode($var)) {
            trigger_error("json_encode: invalid UTF-8 encoding in string,
                cannot proceed.",
                E_USER_WARNING);
            $var = NULL;
         }
         $rewrite = array(
             "\\" => "\\\\",
             "\"" => "\\\"",
           "\010" => "\\b",
             "\f" => "\\f",
             "\n" => "\\n",
             "\r" => "\\r",
             "\t" => "\\t",
             "/"  => $options & JSON_UNESCAPED_SLASHES ? "/" : "\\/",
             "<"  => $options & JSON_HEX_TAG  ? "\\u003C" : "<",
             ">"  => $options & JSON_HEX_TAG  ? "\\u003E" : ">",
             "'"  => $options & JSON_HEX_APOS ? "\\u0027" : "'",
             "\"" => $options & JSON_HEX_QUOT ? "\\u0022" : "\"",
             "&"  => $options & JSON_HEX_AMP  ? "\\u0026" : "&",
         );
         $var = strtr($var, $rewrite);
         //@COMPAT control chars should probably be stripped beforehand, not escaped as here
         if (function_exists("iconv") && ($options & JSON_UNESCAPED_UNICODE) == 0) {
            $var = preg_replace("/[^\\x{0020}-\\x{007F}]/ue", "'\\u'.current(
                unpack('H*', iconv('UTF-8', 'UCS-2BE', '$0')))", $var);
         }
         $json = '"' . $var . '"';
      }

      // basic types
      elseif (is_bool($var)) {
         $json = $var ? "true" : "false";
      }
      elseif ($var === NULL) {
         $json = "null";
      }
      elseif (is_int($var) || is_float($var)) {
         $json = "$var";
      }

      // something went wrong
      else {
         trigger_error("json_encode: don't know what a '" .gettype($var). "' is.",
            E_USER_WARNING);
      }

      // done
      return($json);
   }
}
