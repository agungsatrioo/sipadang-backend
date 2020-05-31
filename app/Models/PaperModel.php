<?php

namespace App\Models;

use CodeIgniter\Model;

class PaperModel extends Model
{
    public function newSheet($content) {
        return "<section class='sheet padding-10mm'>$content</section>";
    }

    public function kopSurat() {
        return ' <div class="row">
        <div class="col-lg-2">
            <img src="'.base_url('public/media/images/uin.png').'">
        </div>
        <div class="col-lg-10 text-center kop-surat">
            <b>KEMENTERIAN AGAMA</b><br>
            <b>UNIVERSITAS ISLAM NEGERI (UIN)</b><br>
            <b>SUNAN GUNUNG DJATI BANDUNG</b><br>
            <b>FAKULTAS ILMU SOSIAL DAN ILMU POLITIK</b><br><br>
            <i>Jl. AH Nasution No.105 Bandung 40614</i><br>
            <i>Website: http://fisipuinsgd.ac.id Telepon/Fax: (022) 7811918</i>
        </div>
    </div>
    <hr>';
    }
}
