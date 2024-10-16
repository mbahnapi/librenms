<?php
/**
 * nokia-isam.inc.php
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @link       https://www.librenms.org
 *
 * @copyright  2019 Vitali Kari
 * @author     Vitali Kari <vitali.kari@gmail.com>
 */

// Use proprietary asamIfExtCustomerId as ifAlias for Nokia ISAM Plattform. The default IF-MIB fields are here quite meaningless
$isam_port_stats = snmpwalk_cache_oid($device, 'asamIfExtCustomerId', [], 'ITF-MIB-EXT', 'nokia-isam');

foreach ($isam_port_stats as $index => $value) {
    $port_stats[$index]['ifAlias'] = $isam_port_stats[$index]['asamIfExtCustomerId'];
	
foreach (array ( 'ifHCInOctets','ifHCInUcastPkts','ifHCInMulticastPkts','ifHCInBroadcastPkts','ifHCOutOctets','ifHCOutUcastPkts','ifHCOutMulticastPkts','ifHCOutBroadcastPkts') as $oid_HEX_Object) {	
		$port_stats[$index][$oid_HEX_Object] = hexdecs(preg_replace("/[^0-9A-Fa-f]/", '', $port_stats[$index][$oid_HEX_Object]));
	}

}
unset($isam_ports_stats);

// -- SOURCE https://www.php.net/manual/en/function.hexdec.php
function hexdecs($hex)
{
    // ignore non hex characters
    $hex = preg_replace('/[^0-9A-Fa-f]/', '', $hex);
   
    // converted decimal value:
    $dec = hexdec($hex);
   
    // maximum decimal value based on length of hex + 1:
    //   number of bits in hex number is 8 bits for each 2 hex -> max = 2^n
    //   use 'pow(2,n)' since '1 << n' is only for integers and therefore limited to integer size.
    $max = pow(2, 4 * (strlen($hex) + (strlen($hex) % 2)));
   
    // complement = maximum - converted hex:
    $_dec = $max - $dec;
   
    // if dec value is larger than its complement we have a negative value (first bit is set)
    return $dec > $_dec ? -$_dec : $dec;
}
