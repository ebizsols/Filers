<?php
namespace Database\Seeders;

use App\Models\LeadAgent;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\UniversalSearch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('leads')->delete();

        DB::statement('ALTER TABLE leads AUTO_INCREMENT = 1');

        $count = env('SEED_PROJECT_RECORD_COUNT', 20);
        $faker = \Faker\Factory::create();

        \App\Models\Lead::factory()->count((int)$count)->create()->each(function ($lead) use($faker) {
            $lead->agent_id = $faker->randomElement($this->getLeadAgent()); /* @phpstan-ignore-line */
            $lead->source_id = $faker->randomElement($this->getLeadSource()); /* @phpstan-ignore-line */
            $lead->status_id = $faker->randomElement($this->getLeadStatus()); /* @phpstan-ignore-line */
            $lead->save();
        });
    }

    private function getLeadAgent()
    {
        return LeadAgent::with('user')->get()->pluck('id')->toArray();
    }

    private function getLeadStatus()
    {
        return LeadStatus::get()->pluck('id')->toArray();
    }

    private function getLeadSource()
    {
        return LeadSource::get()->pluck('id')->toArray();
    }

    public function logSearchEntry($searchableId, $title, $route, $type)
    {
        $search = new UniversalSearch();
        $search->searchable_id = $searchableId;
        $search->title = $title;
        $search->route_name = $route;
        $search->module_type = $type;
        $search->save();
    }

}
