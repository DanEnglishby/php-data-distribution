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
     * @param $arrayData []
     * @param $intLowerToleranceLimit int
     * @param $intUpperToleranceLimit int
     * @param $boolUseCustomTolerances bool
     * @param $intBins int
     */
    function __construct($arrayData, $intLowerToleranceLimit, $intUpperToleranceLimit, $boolUseCustomTolerances, $intBins)
    {
        $this->data = $arrayData;

        // If custom tolerances are set.
        if ($boolUseCustomTolerances) {
            $this->lowerToleranceLimit = $intLowerToleranceLimit;
            $this->upperToleranceLimit = $intUpperToleranceLimit;
        }
        else { // Else use min max of array as tolerances
            $this->lowerToleranceLimit = min($this->data);
            $this->upperToleranceLimit = max($this->data);
        }

        $this->bins = $intBins;
        $this->singleBinValue = ($this->upperToleranceLimit - $this->lowerToleranceLimit) / $this->bins; // Divide by bin to give us bin size. Eg. a 10th of the value of (ucl - lcl)
        $this->DefineDistributionCounters();
        $this->Distribute();
    }

    /**
     * DefineDistributionCounters()
     *
     * @return void
     */
    private function DefineDistributionCounters()
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
            array_push($this->distributionResultsArray, ["Distribution Section" => $lowerBin . "-" . $upperBin, "Count" => $this->{"distribution" . $distributionId}]);
            $distributionId++;
            $lowerBinMultiplier++;
            $upperBinMultiplier++;
        }
    }

    /**
     * DumpDistributionArray()
     *
     * Dump Distribution results array (For debugging.)
     * return var_dump(Array) for debugging.
     */
    public function DumpDistributionArray()
    {
         var_dump($this->distributionResultsArray);
    }

    /**
     * GetDistributionArray()
     *
     * Output Distribution results in an array
     *
     * @return array
     */
    public function GetDistributionArray()
    {
        return $this->distributionResultsArray;
    }

    /**
     * GetDistributionJSON()
     *
     * Output Distribution results in JSON.
     *
     * @return false|string
     */
    public function GetDistributionJSON()
    {
        return json_encode($this->distributionResultsArray);
    }

    /**
     * GetDistributionHTMLTable()
     *
     * Output Distribution results in a string HTML Table.
     * @param $class string
     * @return string
     */
    public function GetDistributionHTMLTable($class)
    {
        $html = "<table class='$class'>";

        $html.= "<thead>
                    <th>Distribution Section</th>
                    <th>Count</th>
                </thead>";
        $html.= "<tbody>";

        foreach ($this->distributionResultsArray as $d) {
            $html.= "<tr><td>".$d["Distribution Section"]."</td><td>".$d["Count"]."</td></tr>";
        }
        $html.= "</tbody>";
        $html.= "</table>";

        return $html;
    }
}

//$obj = new DataDistribution([1, 2, 3, 4, 5, 5, 5, 5, 5, 10, 10, 10, 10, 10], 0, 15, true, 15);
//echo $obj->GetDistributionJSON();
//echo $obj->GetDistributionHTMLTable('table');