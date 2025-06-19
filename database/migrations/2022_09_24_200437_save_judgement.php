<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Submission;
use Illuminate\Support\Str;
class SaveJudgement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('submissions', 'judgement')){

            Schema::table('submissions', function (Blueprint $table) {
                //
                $table->json('judgement');
            });
        }

        $progress = 0;
        Submission::chunk(8000, function($subs) use (&$progress){
            foreach ($subs as  $sub){
                if($sub->judgement != "") continue;
                $path =  $sub->directory() . "/result-" . $sub->id . ".html";
                if (file_exists($path)){
                    $result = mb_convert_encoding( file_get_contents($path), 'UTF-8');
                }
                else{
                    $result = "";
                }
                $results = explode("</span>\n", $result);
                
                $times_and_mem = Arr::flatten(array_filter($results, function($i){return str_contains($i, 'text-muted');}));
                $times = array_map(function($i){ return floatval( Str::before(Str::after($i, "<small>"), " s and")  )  ;},  $times_and_mem);
                $mems = array_map(function($i){ return floatval( Str::before(Str::after($i, "s and "), " KiB")  )  ;},  $times_and_mem);
                
                $testcase_verdict = array_filter($results, function($s){
                    return $s != '' && !Str::contains($s, ['text-muted', 'text-primary', 'text-success']);
                });
    
                $testcase_verdict = array_map( function($s) {return Str::before( Str::after($s, '>'), "<" );}, $testcase_verdict );
                $verdicts = [];
                foreach ($testcase_verdict as $key => $value) {
                    $verdicts[$value] = ($verdicts[$value] ?? 0) + 1;
                }

                $a = [ "times" => $times, "mems" => $mems, "verdicts" => $verdicts ]  ;
                // echo($result . "\n");
                // echo($a );
                // echo("-------------\n");
                try{
                    $sub->judgement = $a;
                    $sub->save();
                } catch (Exception $e){
                    echo 'Caught exception: ',  $e->getMessage(), "\n";
                    echo 'error processing ', $sub->id;
                    var_dump($a); 
                }
            }
            $progress += $subs->count();
            echo ("----Process " . $progress . "\n");
        });
        

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('submissions', 'judgement')){

            Schema::table('submissions', function (Blueprint $table) {
                //
                $table->dropColumn('judgement');
            });
        }
    }
}
