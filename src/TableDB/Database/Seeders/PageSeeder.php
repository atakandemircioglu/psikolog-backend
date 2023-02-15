<?php

class PageSeeder extends Seeder
{
    protected $table = "PAGES";
    public function run()
    {
        $this->seed();
    }
    
    protected function seed()
    {
        // CLEAR ALL DATA IN TABLE
        $this->clear();
        echo "Cleaning done";

        // INSERT DATA INTO TABLE
        echo "seeds are building..\n.";
        $techDocs = new PageModel(["title" => "Technical Docs", "slug" => "techDocs"]);
        $gettingStarted = new PageModel(["title" => "Getting Started", "slug" => "gettingStarted"]);

        $tutorials = new PageModel(["title" => "Tutorials", "slug" => "tutorials"]);
        $tutorials1 = new PageModel(["title" => "Tutorial 1", "slug" => "tutorial1", "up_slug" => "tutorials"]);
        $tutorials2 = new PageModel(["title" => "Tutorial 2", "slug" => "tutorial2", "up_slug" => "tutorials"]);

        $tutorialSub1 = new PageModel(["title" => "Tutorials_sub1", "slug" => "tutorials_sub1", "up_slug" => "tutorial1"]);
        $tutorialSub2 = new PageModel(["title" => "Tutorials_sub2", "slug" => "tutorials_sub2", "up_slug" => "tutorial1"]);
        $tutorialSub3 = new PageModel(["title" => "Tutorials_sub3", "slug" => "tutorials_sub3", "up_slug" => "tutorial1"]);

        

        $howToGuides = new PageModel(["title" => "How-to Guides", "slug" => "howToGuides"]);
        $howToGuidesSub1 = new PageModel(["title" => "How-to Guide 1", "slug" => "howToGuide1", "up_slug" => "howToGuides"]);
        $howToGuidesSub2 = new PageModel(["title" => "How-to Guide 2", "slug" => "howToGuide2", "up_slug" => "howToGuides"]);

        $test1_1 = new PageModel(["title" => "Test 1_1", "slug" => "test1_1", "up_slug" => "test1"]);
        $test1_2 = new PageModel(["title" => "Test 1_2", "slug" => "test1_2", "up_slug" => "test1"]);
        $test1 = new PageModel(["title" => "Test 1", "slug" => "test1", "up_slug" => "test"]);

        $moreSub1 = new PageModel(["title" => "More Sub 1", "slug" => "more_sub_1", "up_slug" => "more_test_sub_1"]);
        $moreSub2 = new PageModel(["title" => "More Sub 2", "slug" => "more_sub_2", "up_slug" => "more_test_sub_1"]);
        $moreSub3 = new PageModel(["title" => "More Sub 3", "slug" => "more_sub_3", "up_slug" => "more_test_sub_1"]);
        $moreTestSub1 = new PageModel(["title" => "More Test Sub 1", "slug" => "more_test_sub_1", "up_slug" => "more_test_1"]);
        $moreTestSub2 = new PageModel(["title" => "More Test Sub 2", "slug" => "more_test_sub_2", "up_slug" => "more_test_1"]);
        $moreTestSub3 = new PageModel(["title" => "More Test Sub 3", "slug" => "more_test_sub_3", "up_slug" => "more_test_1"]);
        $moreTest1 = new PageModel(["title" => "More Test 1", "slug" => "more_test_1", "up_slug" => "more_test"]);

        $tutorial2 = new PageModel(["title" => "Tutorial 2", "slug" => "tutorial2", "up_slug" => "more_test"]);
        $tutorial3 = new PageModel(["title" => "Tutorial 3", "slug" => "tutorial3", "up_slug" => "more_test"]);
        $tutorial4 = new PageModel(["title" => "Tutorial 4", "slug" => "tutorial4", "up_slug" => "more_test"]);
        $moreTest = new PageModel(["title" => "More Test", "slug" => "more_test"]);
        
        $array = [$techDocs, $gettingStarted, $tutorials, $tutorials1, $tutorials2, $tutorialSub1, $tutorialSub2, $tutorialSub3,
        $howToGuides, $howToGuidesSub1, $howToGuidesSub2, $test1_1, $test1_2, $test1, $moreSub1, $moreSub2, $moreSub3,
        $moreTestSub1, $moreTestSub2, $moreTestSub3, $moreTest1, $tutorial2, $tutorial3, $tutorial4, $moreTest];

        echo "seeds are inserting..\n";
        foreach ($array as $page) {
            $modelArrays[] = $page->toArray();
        }

        SheetDB::table((new PageModel())->tableName)->insert($modelArrays);
        return true;
    }
}
