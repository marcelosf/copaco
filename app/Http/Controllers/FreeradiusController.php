<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rede;
use App\Equipamentos;

use App\Utils\NetworkOps;

use IPTools\IP;
use IPTools\Network;
use IPTools\Range;

class FreeradiusController extends Controller
{
    public function build()
    {
        $ops = new NetworkOps;
        $build = "";

        $redes = Rede::all();
        foreach ($redes as $rede) {
            if (isset($rede->vlan) && !empty($rede->vlan)) {
                foreach ($rede->equipamentos as $equipamento) {
                    $macaddress = strtolower(str_replace(':', '', $equipamento->macaddress));
                    $build .= "$macaddress   Cleartext-Password := $macaddress\n";
                    $build .= "    Tunnel-Type = \"VLAN\"\n";
                    $build .= "    Tunnel-Medium-Type = \"IEEE-802\",\n";
                    $build .= "    Tunnel-Private-Group-Id = \"1188\"\n\n";
                }
            }
        }
    
        $build .= "DEFAULT Framed-Protocol == PPP\n";
        $build .= "    Framed-Protocol = PPP,\n";
        $build .= "    Framed-Compression = Van-Jacobson-TCP-IP\n\n";

        $build .= "DEFAULT Hint == \"CSLIP\"\n";
        $build .= "    Framed-Protocol = SLIP,\n";
        $build .= "    Framed-Compression = Van-Jacobson-TCP-IP\n\n";

        $build .= "DEFAULT Hint == \"SLIP\"\n";
        $build .= "    Framed-Protocol = SLIP";

        return response($build)->header('Content-Type', 'text/plain');
    }

    public function sincronize()
    {
        $redes = Rede::all();
        $host=getenv('FREERADIUS_HOST');
        $db=getenv('FREERADIUS_DB');
        $pdo = new \PDO("mysql:host={$host};dbname={$db}", getenv('FREERADIUS_USER'), getenv('FREERADIUS_PASSWD'));
        foreach ($redes as $rede) {
            $sql = "INSERT INTO radgroupreply(groupname,attribute,op,value) VALUES (?,?,?,?)";
            $stmt= $pdo->prepare($sql);
            $stmt->execute([$rede->id,'Tunnel-Type',':=','VLAN']);
            $stmt->execute([$rede->id,'Tunnel-Medium-Type',':=','IEEE-802']);
            $stmt->execute([$rede->id,'Tunnel-Private-Group-Id',':=',$rede->vlan]);
            foreach ($rede->equipamentos as $equipamento) {
                $sql = "INSERT INTO radusergroup(UserName,GroupName) VALUES (?,?)";
                $stmt= $pdo->prepare($sql);
                $stmt->execute([$equipamento->macaddress,$rede->id]);
            }
        }
        return redirect('/');
    }

}

