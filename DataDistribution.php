<?php

namespace DanEnglishby\Tools\DataDistribution;

class DataDistribution
{
    private $data = null;
    private $lowerToleranceLimit = 0;
    private $upperToleranceLimit = 0;
    private $bins = 0;
    private $singleBinValue = 0;
    private $distributionResultsArray = [];

    /**
     * DataDistribution constructor.
     * @param $d
     * @param $lcl
     * @param $ucl
     * @param $b
     */
    function __construct($d, $lcl, $ucl, $b)
    {
        $this->data = $d;
        $this->lowerToleranceLimit = $lcl;
        $this->upperToleranceLimit = $ucl;
        $this->bins = $b;
        $this->singleBinValue = ($this->upperToleranceLimit - $this->lowerToleranceLimit) / $this->bins; // Divide by bin to give us bin size. Eg. a 10th of the value of (ucl - lcl)
        $this->DefineDistributionCounters();
    }

    /**
     * DefineDistributionCounters()
     *
     * @return void
     */
    public function DefineDistributionCounters()
    {
        $distributionId = "A";
        // Define all distribution counter variables... Example - $distributionA, $distributionB
        for ($i = 0; $i < $this->bins; $i++) {
            $this->{"distribution" . $distributionId} = 0;
            $distributionId++;
        }
    }

    /**
     * Distribute()
     *
     * @return void
     */
    public function Distribute()
    {
        foreach ($this->data as $value) {
            $distributionId = "A";

            if ($value >= $this->lowerToleranceLimit && $value <= $this->upperToleranceLimit) {
                $binFound = false;
                $lowerBinMultiplier = 0;
                $upperBinMultiplier = 1;
                while (!$binFound) {
                    if ($value >= ($this->lowerToleranceLimit + ($this->singleBinValue * $lowerBinMultiplier)) && $value <= ($this->lowerToleranceLimit + ($this->singleBinValue * $upperBinMultiplier))) {
                        $this->{"distribution" . $distributionId}++;
                        $binFound = true;
                    }
                    $lowerBinMultiplier++;
                    $upperBinMultiplier++;
                    $distributionId++;
                }
            }
        }

        // Reset multipliers and distributionId so we can calculate + access values and add to array.
        $lowerBinMultiplier = 0;
        $upperBinMultiplier = 1;
        $distributionId = "A";

        for ($i = 0; $i < $this->bins; $i++) {
            $lowerBin = ($this->lowerToleranceLimit + ($this->singleBinValue * $lowerBinMultiplier));
            $upperBin = ($this->lowerToleranceLimit + ($this->singleBinValue * $upperBinMultiplier));
            array_push($this->distributionResultsArray, ["Bin" => $lowerBin . "-" . $upperBin, "count" => $this->{"distribution" . $distributionId}]);
            echo "Bin " . $lowerBin . "-" . $upperBin . ":  ( " . $this->{"distribution" . $distributionId} . " )";
            echo "<br />";

            $distributionId++;
            $lowerBinMultiplier++;
            $upperBinMultiplier++;
        }

        var_dump($this->distributionResultsArray);
    }

    /**
     * Dump Distribution results array (For debugging.)
     */
    public function DumpDistributionArray()
    {
        // var_dump();
    }

    /**
     * Output Distribution results in an array
     */
    public function GetDistributionArray()
    {

    }

    /**
     * Output Distribution results in JSON.
     */
    public function GetDistributionJSON()
    {

    }

    /**
     * Output Distribution results in a string HTML Table.
     */
    public function GetDistributionHTMLTable()
    {

    }
}

$obj = new DataDistribution([1, 2, 3, 4, 5, 5, 5, 5, 5, 10, 10, 10, 10, 10], 0, 15, 15);
$obj->Distribute();