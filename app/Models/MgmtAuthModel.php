<?php

namespace App\Models;

use CodeIgniter\Model;
use Firebase\JWT\JWT;


class MgmtAuthModel extends Model
{
    public function getPrivateKey()
    {
        $private_key = <<<EOD
        -----BEGIN RSA PRIVATE KEY-----
        MIIEowIBAAKCAQEAmCJU91/PmXIDh5QX2KPjpJKqYdeIMGV74sE9KK5GxYpHzg2I
        lQfVAW8a5JA6ZqRS62u89w0snxGaNovPgOH2iOX2E8JE0ixkzM80i88DfqkTcJkN
        5Plhn4zuYQdzjX8Khlvbj0ZFIp7VQcPKiLLY7zMJ5iQ2K/cRZFUIACgQ+RbuR+VQ
        pfDxyEJuIZFgUTEacrGolpxqTjr7fBRwEeMpyx9b67XgVBdw4fAHU6oGBvuCOHTd
        ip1hFUyUOpfFnbE90cne8zZ0z/ScqgrURsHyn5Srl0HIHXqUCEL7iJDOyvsSua7b
        WLd7vd3O0sDNgStGmJ4DIJKzgUkkQv8pBOWPxQIDAQABAoIBAC7KmHGBYXujipxf
        mzoBplmO1qLDRNsFy6XBo4rol1HYpx8kIHcd8pC/WHkmNyAsuGg6OeOOhMPkCRdv
        xwGv/kC64gXPwZUXHGW18UZzBHMnk5gVKyXa4gDNut/TB+JkSZtrk0ss5MSyL4tL
        qfaDc5+Whhvn1VWOZyYB1TUfHaFOxrGAIaJrfMD3gtqAEsnMVxM+AHWwCah9kMoY
        DwaU4xfepTaecKJWhqKKRp/sjADyqQ9/FLx8rAlNTojQWjJCzyX6OAJ3WJ5sqzRo
        zf9NoDAI9UaaaQAaarBgy9wY1NFwwlDkcdY3e820JSqxwTjsvV3t/J8ydV5wSWja
        7oC+PeECgYEA4jQRlINv5SxvQCmDBjC3ASDZUb/6vdvphgaGJUpWn7ZzXAz9S7ei
        +t7fIdxJTUPIcdwPXjcGlvZYItp6i59UoqeApeqX1I2H6Z+cLodHco+CQxEwrIhq
        dGg9B13m+CctCYqAewOea1g7TLVxuRkn0Sd1EXxD3UAltz4w2NOPp70CgYEArCyI
        ChUNjO1BgmaC8yrJt/1pmKsDK2EegGyD4FfDFB+Y8q8JKSTd6XT2pIoyjpqZ0mto
        DBOmiADjjbVSak96f1duUg6pE8jKR6igh3GUY9JVeYJzg8Tf07FNXVOWdXdlsXr+
        B5mF58dww0gipHULZIrI3y+alkjJ0H+Zzj9SZKkCgYAbZDK8yS3Dkp3mJyC2ny+J
        83BddeHnG7orgJ695UKYFH/jpa8GfDZAKrzaXNKDiLG9F+jEf+VMYohJsCsmvSSC
        jcYGUWIRWxIaODmxaLA+LyKW/H3oZpx9fCHnwnc1lYjenubv/oAwf36uy0n6IBGy
        QhTrCSEuqZXnmqBPoU7EAQKBgGgQ0nklmHchwILM60GHDz3CUd0RIG1L2l/NNziW
        UzcKkiu9WFAiFG9TXvC9BynikC769Hy4M+PohaHVgub2+xeVBP1cFx36Myl05vJ2
        2DYtn7Q+AQwYJyGQ/S33o+2EKVdI+9okFSevCJ1AMJAunqA6socUAt/rRvBa7+AV
        rfthAoGBAJOc2U4z9gdJkp8RBR8S/ncGNA13Z7BdCV1pWSomfyZLillM/gaffQh9
        QWDpAkctQ3yFLu1a4goIRPtvVBOsfPhF/HwzYXNlA41/2zTYuA9qKqSrfLTDoKxW
        s/07dpGCNXk9sBmtwPhu1BHGfQOG20RFwFQm2KubyFwoWnD0VnzF
        -----END RSA PRIVATE KEY-----
EOD;
        return $private_key;
    }

    public function getUser($identity)
    {
        return $this->db->table("t_mgmt_user")
            ->where("username", $identity)
            ->get()->getFirstRow();
    }

    public function auth($identity, $password, $type = "")
    {
        $mhsModel = new \App\Models\MahasiswaModel($this->db);
        $dosenModel = new \App\Models\DosenModel($this->db);

        if (empty($identity))  return ["status" => "failed", "code" => 401, "msg" => "Pengguna tidak boleh kosong."];
        if (empty($password))  return ["status" => "failed", "code" => 401, "msg" => "Kata sandi tidak boleh kosong."];

        $user = $this->getUser($identity);

        if(empty($user)) return ["status" => "failed", "code" => 401, "msg" => "Pengguna tidak ditemukan."];

        if (password_verify($password, $user->password)) {
            $user_data = array(
                "id_user"    => $user->id_user,
                "username"    => $user->username,
                "last_login" => date('Y-m-d H:m:s', time())
            );

            if ($type != "verify") $user_data['token'] = $this->makeJwtToken($user_data);

            return ["status" => "ok", "code" => 200, "data" => $user_data];
        } else {
            return ["status" => "failed", "code" => 401, "msg" => "Kata sandi salah."];
        }
    }

    private function makeJwtToken($data)
    {
        $secret_key = $this->getPrivateKey();
        $issuer_claim = "SIPADANG-MGMT-FISIP";
        $audience_claim = "SIPADANG_CLAIM";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 1;
        $expire_claim = $issuedat_claim + 432000; //token will be expired within 6 days

        $token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => $data
        );

        return JWT::encode($token, $secret_key);
    }
}
