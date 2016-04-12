<?php header('Access-Control-Allow-Origin: *'); ?>
<?php   //echo $_GET["street"];
           

            date_default_timezone_set('America/Los_Angeles');
    		error_reporting(E_ALL ^ E_WARNING);
			function formatDate($oldDate)
			{
				$newDate=DateTime::createFromFormat('m/d/Y',$oldDate);
				$newFormDate=$newDate->format('d-M-Y');
				return $newFormDate;
			}

			function prepareChangeStr($chStr,$amt)
			{
				if ($amt < 0)
					$newChStr=$chStr." <img src='http://www-scf.usc.edu/~csci571/2014Spring/hw6/down_r.gif'>";
				else
					$newChStr=$chStr." <img src='http://www-scf.usc.edu/~csci571/2014Spring/hw6/up_g.gif'>";
				return $newChStr;
			}

			function getAmt($amt)
			{
				if($amt<0)
					return " <img src='http://www-scf.usc.edu/~csci571/2014Spring/hw6/down_r.gif'>"."$".number_format((double)abs($amt),2,".",",");
				else
					return " <img src='http://www-scf.usc.edu/~csci571/2014Spring/hw6/up_g.gif'>"."$".number_format((double)$amt,2,".",",");
			}
			/*
			function buildArray()
			{
				$detArr=array("Property Type" => $result->useCode,"Last Sold Price" => number_format($result->lastSoldPrice,2,".",","),"Year Built" => $result->yearBuilt,"$lastSoldStr" => $lastSoldDate,"Lot Size" => $result->lotSizeSqFt.' sq. ft.',$propEstStr => $propEstAmt,"Finished Area" => $result->finishedSqFt.' sq. ft.',$valueChangeStr => $valueChangeAmt,"Bathrooms" => $result->bathrooms,$RangeStr => $RangeAmt,"Bedrooms" => $result->bedrooms,$rentEstStr => $rentEstAmt,"Tax Assessment Year" => $result->taxAssessmentYear,$rentValueChangeStr => $rentValueChangeAmt,"Tax Assessment" => number_format($result->taxAssessment,2,'.',','),$rentRangeStr => $rentRangeAmt);
			}
			*/ 
	?> 
<?php 
	$street=$_GET["street"];
	$city=$_GET["city"];
	$state=$_GET["state"];
	$arr=array("street" => $street,"city" => $city,"state" => $state);
	//echo (json_encode($arr)); 
	//echo ($_GET["street"] . $_GET["city"] . $_GET["state"]); 
	$stradr=$_GET["street"];
    $citystatezip=$_GET["city"].", ".$_GET["state"];
    $data = array(  'address'=> $stradr,'citystatezip'=> $citystatezip,'rentzestimate' => 'true');
    $urlstr="http://www.zillow.com/webservice/GetDeepSearchResults.htm?zws-id=X1-ZWz1dxi3d1oemj_5upah&".http_build_query($data);
	//echo ("<h2> The url is=".$urlstr."</h2>");
	$xml=simplexml_load_file($urlstr);
	//$result=$xml->response->results->result[0];
	//echo ($result->lastSoldDate.$xml->message->text);
	if($xml->message->code == 0)
				{
					$result=$xml->response->results->result[0];
					//$updateURL="http://www.zillow.com/webservice/GetUpdatedPropertyDetails.htm?zws-id=X1-ZWz1dxi3d1oemj_5upah&zpid=".$result->zpid;
					//$updateXML=simplexml_load_file($updateURL);
					$chartsURL1="http://www.zillow.com/webservice/GetChart.htm?zws-id=X1-ZWz1dxi3d1oemj_5upah&unit-type=percent&zpid=".$result->zpid."&width=600&height=300&chartDuration=1year";
					$chartsXML1=simplexml_load_file($chartsURL1);	
					$chartsURL5="http://www.zillow.com/webservice/GetChart.htm?zws-id=X1-ZWz1dxi3d1oemj_5upah&unit-type=percent&zpid=".$result->zpid."&width=600&height=300&chartDuration=5years";
					$chartsXML5=simplexml_load_file($chartsURL5);	
					$chartsURL10="http://www.zillow.com/webservice/GetChart.htm?zws-id=X1-ZWz1dxi3d1oemj_5upah&unit-type=percent&zpid=".$result->zpid."&width=600&height=300&chartDuration=10years";
					$chartsXML10=simplexml_load_file($chartsURL10);	


					
					//for($i=0;$i<$xml->response->results->result->count();$i++)
					//{
					//echo 'result-count :', $xml->r
						$newFormDate = "";
						$lastSoldDate = "";
						$propEstStr = "Zestimate&reg Property Estimate as of";
						$propEstAmt = "";
						$valueChangeStr = "30 Days Overall Change";
						$valueChangeAmt = "";
						$rangeStr = "All Time Property Range";
						$rangeAmt = "";
						$rentEstStr = "Rent Zestimate&reg Valuation as of";
						$rentEstAmt = "";
						$rentValueChangeStr = "30 Days Rent Change";
						$rentValueChangeAmt = "";
						$rentRangeStr = "All Time Rent Range";
						$rentRangeAmt = "";
						//$result=$xml->response->results->result[0];
						$lastSoldStr="Last Sold Date";
						if ($result->lastSoldDate!="")
						{
							$lastSoldDate=formatDate($result->lastSoldDate);
							//echo '<br>$lastSoldDate=',$lastSoldDate;
							
						}
			//This is for Zestimate
						if((double)$result->zestimate->amount>0.0)

						{	$newFormatLastUpdate=formatDate($result->zestimate->{'last-updated'});
							//echo '<br>$newFormatLastUpdate=',$newFormatLastUpdate;
							$propEstStr="Zestimate&reg Property Estimate as of ".$newFormatLastUpdate;
							$propEstAmt="$".number_format((double)$result->zestimate->amount,2,".",",");
							//echo '<br>$propEstStr=',$propEstStr;
							//echo '<br>$propEstAmt=',$propEstAmt;
						}
                        //echo "value= ".$result->zestimate->valueChange;
                        //echo "isset=".isset($result->zestimate->valueChange);
                        //echo "empty=".empty($result->zestimate->valueChange);
						if(abs((double)$result->zestimate->valueChange)>0.0)	
						{	
							$valueChangeStr="30 Days Overall Change";
							//$valueChangeStr=prepareChangeStr("30 Days Overall Change",$result->zestimate->valueChange);
							$valueChangeAmt=getAmt($result->zestimate->valueChange);
							if ($result->zestimate->valueChange > 0) $zestChangeSign='+';
							else $zestChangeSign='-';

							//echo '<br>$valueChangeStr=',$valueChangeStr;
							//echo '<br>$valueChangeAmt=',$valueChangeAmt;
						}   
						if((double)$result->zestimate->valuationRange->low>0.0)
						{
							$rangeStr="All Time Property Range";
							$rangeAmt="$".number_format((double)$result->zestimate->valuationRange->low,2,".",",")." - $".number_format((double)$result->zestimate->valuationRange->high,2,".",",");
							//echo '<br>$rangeAmt=',$rangeAmt;
						}
			//This is for rent zestimate
						if((double)$result->rentzestimate->amount>0.0)

						{	$rnewFormatLastUpdate=formatDate($result->rentzestimate->{'last-updated'});
							$rentEstStr="Rent Zestimate&reg Valuation as of ".$rnewFormatLastUpdate;
							$rentEstAmt="$".number_format((double)$result->rentzestimate->amount,2,".",",");
							//echo '<br>$rentEstStr=',$rentEstStr;
							//echo '<br>$rentEstAmt=',$rentEstAmt;
						}
						if(abs((double)$result->rentzestimate->valueChange)>0.0)
						{
							//$rentValueChangeStr=prepareChangeStr("30 Days Rent Change",$result->rentzestimate->valueChange);
							$rentValueChangeStr="30 Days Rent Change";
							$rentValueChangeAmt=getAmt($result->rentzestimate->valueChange);
							if ($result->rentzestimate->valueChange > 0) $rentChangeSign='+';
							else $rentChangeSign='-';
							//echo '<br>$rentValueChangeAmt=',$rentValueChangeAmt;
							//echo '<br>$rentValueChangeStr=',$rentValueChangeStr;
						}   
						if((double)$result->rentzestimate->valuationRange->low>0.0)
						{
							$rentRangeStr="All Time Rent Range";
							$rentRangeAmt="$".number_format((double)$result->rentzestimate->valuationRange->low,2,".",",")." - $".number_format((double)$result->rentzestimate->valuationRange->high,2,".",",");
							//echo '<br>$rentRangeAmt=',$rentRangeAmt;
						}
						if ($chartsXML1->message->code == 0) $chart1 = (string)$chartsXML1->response->url;
						else $chart1="";
						if ($chartsXML5->message->code == 0) $chart5 = (string)$chartsXML5->response->url;
						else $chart5="";
						if ($chartsXML10->message->code == 0) $chart10 = (string)$chartsXML10->response->url;
						else $chart10="";

						$detArr=array("Property Type" =>(string)$result->useCode,"Last Sold Price" => ((double)$result->lastSoldPrice > 0 ? '$'.number_format((double)$result->lastSoldPrice,2,".",",") : ''),"Year Built" => (string)$result->yearBuilt,$lastSoldStr => $lastSoldDate,"Lot Size" => ((double)$result->lotSizeSqFt > 0.0 ? number_format((int)$result->lotSizeSqFt).' sq. ft.' : ''),$propEstStr => $propEstAmt,"Finished Area" => ((double)$result->finishedSqFt > 0.0 ? number_format((int)$result->finishedSqFt).' sq. ft.' : ''),$valueChangeStr => $valueChangeAmt,"Bathrooms" => (string)$result->bathrooms,$rangeStr => $rangeAmt,"Bedrooms" => (string)$result->bedrooms,$rentEstStr => $rentEstAmt,"Tax Assessment Year" => (string)$result->taxAssessmentYear,$rentValueChangeStr => $rentValueChangeAmt,"Tax Assessment" => ((double)$result->taxAssessment > 0 ? '$'.number_format((double)$result->taxAssessment,2,'.',',') : ''),$rentRangeStr => $rentRangeAmt,"zestChange" => $zestChangeSign,"rentChange" => $rentChangeSign,"year1" => $chart1 ,"year5" => $chart5,"year10" => $chart10,"homedetails" => (string)$result->links->homedetails, "street" => (string)$result->address->street, "city" => (string)$result->address->city, "state" => (string)$result->address->state, "zipcode" => (string)$result->address->zipcode);
						$jsonStr=json_encode($detArr);
						echo $jsonStr;
						//print_r($detArr);
						/*
                        $tstr="";
						$tstr.= "<p class='propText'> See more details for <a class='propLink' style='text-decoration : none; color:blue;' target='_blank' href=".(string)$result->links->homedetails.">".(string)$result->address->street.", ".(string)$result->address->city.", ".(string)$result->address->state."-".(string)$result->address->zipcode."</a> on Zillow</p>";
						$tstr.= "<table>";
						$newrow=0;
						
						foreach($detArr as $key => $value)
						{
							if($newrow%2==0)
							{
								$tstr.="<tr>";
							}
							
							$tstr.="<td class='left'><b>".$key." :</td><td class='right'>".$value."</td>";
							$newrow++;
							if($newrow%2==0)
							{
								$tstr.="</tr>";
							}
							
						}	
						$tstr.="</table>"; 
						$tstr.="<p style='margin-left:450px;'>&copy Zillow, Inc., 2006-2014. Use is subject to <a target='_blank' href='http://www.zillow.com/corp/Terms.htm'> Terms of Use </a></p>";
						$tstr.="<p style='margin-left:550px;margin-top:-10px;font-family:sans-serif;'><a target = '_blank' href='http://www.zillow.com/zestimate/'> What's a Zestimate </a></p>";
                        echo $tstr; */
					//}
				}	

		else

				{
					echo "error";
				}

?>