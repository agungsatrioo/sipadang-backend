<?php

namespace App\Models;

use CodeIgniter\Model;
use Firebase\JWT\JWT;


class AuthModel extends Model
{
    public function getPrivateKey()
    {
        $private_key = <<<EOD
        -----BEGIN RSA PRIVATE KEY-----
        MIIEoQIBAAKCAQBW0kpob5LcswlY9X1iZ+i9c+3aJ7MIdr1aXClVjibUdBkuqokL
        USO4NmtXVfLE4VapNNIdEsyRX5o7cF5WnUGGI02ZfSLSP7LMd1lFTn2wBQxFO2en
        LmsXIGcGELPPSvGx2YZWgeFBELFO+7yEFYel9BCRLdW4XJ61v9LX1vK/93W80eW0
        /BVUSr1+3/jl+2kPgG53KZy2lO9EVISAG5TeCbs0vkhuG7Ztq/KsPQ1xCl51kC1L
        IgQo7eUGdR+fTKMPx+cnN2jvcHZgb8G2A4JYa6rJp4D02D/MIccyMOWXT4w4YRR9
        qFki0NnQE2M06w+NFyjsNkqP8+748RWIlCevAgMBAAECggEAIBA7cIQ7/YuVGShY
        LN5pAoZswdNFeAu59EPy8+iyiGVz41sRj39grKhUTgWsyW7avVSHgDswF0PyRp9C
        B8S9rRcekl+3W2Qw2Pf+nOzW2AmVNYAx0HkBDaJmycOjVBu+VMsbpJkEoi3S/XNU
        dIcq+GvBLox50ENMTZzQ9eso7SG9OBl9fvNg5x4vjxHsOjKIfZy3yJXXKQTyhmi5
        YTaHTuUJFspnI+13b/86GOxkBGe7t2Sm/yhZ0KvS0qOauvxpaepWFiTqqCsOBoXe
        Yd3bb8ELJQwSWgI6u7zj6w8F741Fy2hDWikSYIHT2ulJg2pT9VHrtX4cGPjq63au
        AYhL8QKBgQCcbRp/eO1TqX7/8XFdMBdz33SMdK6PnRXBZGg1aBJINnJsVQCvnD1l
        Fxi9Y9BJgcJpmE7iojJQPLLqKO7KRRve7yItupVTHTa+Jp9Z6ciuLZw8Ej/rbuTN
        1z+YiTaLe1JgRBKPtXLBVbTcaIdi8ztjFhoNXQVkmKn1A5aU2gFSZwKBgQCOFooE
        URjWj/4HIqZueysacxqJ5f1auicbj9G9w4k5pJgUlboLMrpfXoWlrLZ1FXTzXGkG
        q/J5bj0UX2XN+qJyFlkKIkZ/1ejNT4e7AuwGghwDMr46cS6IONB1YTkLCeZU9Hb6
        oPakcgbib3UF2DDeyMXc1QjAZ8xqY9jiefsDeQKBgC+huVPfisTS8+0TpCVwI3QR
        Mvgh/5WTi7Bb1q+MhSSgD6+VIqRWuwsxQUNKRX5cmMp2qm6wXQm5sfFDjZLiAF6e
        CpZKHnY0ixrTv+otGgKPuOdB11zlY56aUK8t+QH4B1lw4QYJhmwAoRYMbk1fK1I8
        xKoVDTNYmUgWU5/30jc5AoGAeyralQdfio7jW3gT4W1vXcwtUyBE0KLRR7kRzXd0
        ur0M/7sSvKZKnGUpYQYoW7Iv4M1YVWo0FEMVO1W+wCDlNBRfNsOjbSkWVvL764aK
        5tFeSv9vmuWFupvVSArxEbqRKU+I25UweDhH830+acSQCG7t5ZHdtjvEHO1Ukm2+
        w7ECgYB5tAcGqItj3rg53lsUCdv8jYwqfeTDPanACYDWQJDktNdLYLPtJqLRIz6K
        bP6YVLYimo4x4AlTc/kbbDtWdUfBLq7BmNDA9MDTu7l+6R4i5rJTDQ2A6yV0U8GF
        9iKCnIMnumDBtK7wCFfGeJxHrLRHMQ8kG/sKR5bY6xSLis+n3Q==
        -----END RSA PRIVATE KEY-----
EOD;
        return $private_key;
    }

    public function getUser($identity)
    {
        return $this->db->table("t_pengguna")
            ->where("identity", $identity)
            ->get()->getFirstRow();
    }

    public function getUsergroup($level)
    {
        return $this->db->table("t_grup_pengguna")
            ->where("id", $level)
            ->get()->getFirstRow();
    }

    public function auth($identity, $password, $type = "")
    {
        $mhsModel = new \App\Models\MahasiswaModel($this->db);
        $dosenModel = new \App\Models\DosenModel($this->db);

        if (empty($identity))  return ["status" => "failed", "code" => 401, "msg" => "Pengguna tidak boleh kosong."];
        if (empty($password))  return ["status" => "failed", "code" => 401, "msg" => "Kata sandi tidak boleh kosong."];

        $cekMhs = $mhsModel->findMahasiswa($identity);
        $cekDosen = $dosenModel->findDosen($identity);

        if (!empty($cekMhs->getRowArray())) {
            $identity = $cekMhs->getFirstRow()->user_identity;
        } elseif (!empty($cekDosen->getRowArray())) {
            $identity = $cekDosen->getFirstRow()->user_identity;
        } else {
            return ["status" => "failed", "code" => 401, "msg" => "Pengguna tidak ditemukan."];
        }

        $user = $this->getUser($identity);

        if(empty($user)) return ["status" => "failed", "code" => 401, "msg" => "Pengguna tidak ditemukan."];

        if (password_verify($password, $user->password)) {
            $u_level    = $this->getUsergroup($user->level);
            $jenis      = $u_level->description;

            $newdata = array(
                "user_level" => $u_level->id,
                "last_login" => date('Y-m-d H:m:s', time())
            );

            $user_data = [];

            switch ($u_level->id) {
                case 5:
                    $query = $dosenModel->findDosen($identity);
                    $user_data = $query->getFirstRow('array');
                    break;
                case 4:
                    $query = $mhsModel->findMahasiswa($identity);
                    $user_data = $query->getFirstRow('array');

                    break;
                default:
                    return ["status" => "failed", "code" => 401, "msg" => "Kesalahan dalam login."];
            }

            if ($type != "verify") $user_data['token'] = $this->makeJwtToken($user_data);

            return ["status" => "ok", "code" => 200, "data" => array_merge($newdata, $user_data)];
        } else {
            return ["status" => "failed", "code" => 401, "msg" => "Kata sandi salah..."];
        }
    }

    private function makeJwtToken($data)
    {
        $secret_key = $this->getPrivateKey();
        $issuer_claim = "SIPADANG-FISIP";
        $audience_claim = "SIPADANG_AUDIENCE_CLAIM";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 1;
        $expire_claim = $issuedat_claim + 1296000; //token will be expired within 15 days

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

    public function createUserMahasiswa($identity)
    {
        $mhsModel = new \App\Models\MahasiswaModel($this->db);

        if (empty($identity))  return ["status" => "failed", "code" => 401, "msg" => "Identitas tidak boleh kosong."];

        $cekMhs = $mhsModel->findMahasiswa($identity);
        $cekUser = $this->getUser($identity);

        if (!empty($cekMhs->getRowArray())) {
            $identity = $cekMhs->getFirstRow()->user_identity;
        } else {
            return ["status" => "failed", "code" => 401, "msg" => "Mahasiswa/dosen tidak ditemukan."];
        }

        if(!empty($cekUser)) return ["status" => "failed", "code" => 401, "msg" => "Pengguna sudah ada."];

        $result = $this->db->table("t_pengguna")
            ->insert([
                "identity" => $identity,
                "password"  => password_hash("mahasiswa", PASSWORD_DEFAULT),
                "level" => "4"
            ]);

        if($result) {
            return ["status" => "ok", "code" => 200];
        } else {
           return ["status" => "failed", "code" => 401, "msg" => "Gagal membuat pengguna"];
        }
    }

    public function resetLupaPassword($identity) {
        $mhsModel = new \App\Models\MahasiswaModel($this->db);
        $dosenModel = new \App\Models\DosenModel($this->db);

        if (empty($identity))  return false;

        $cekMhs = $mhsModel->findMahasiswa($identity);
        $cekDosen = $dosenModel->findDosen($identity);

        if (!empty($cekMhs->getRowArray())) {
            $identity = $cekMhs->getFirstRow()->user_identity;
        } elseif (!empty($cekDosen->getRowArray())) {
            $identity = $cekDosen->getFirstRow()->user_identity;
        } else {
            return false;
        }

        $result = $this->db->table("t_pengguna")
        ->where("identity", $identity)
        ->update(["password_changed" => 0, "password" => password_hash("mahasiswa", PASSWORD_DEFAULT)]);

        return $result;


    }
}
