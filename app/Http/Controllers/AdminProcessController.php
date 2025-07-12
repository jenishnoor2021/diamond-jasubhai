<?php

namespace App\Http\Controllers;

use App\Models\Daily;
use App\Models\Party;
use App\Models\Dimond;
use App\Models\Worker;
use App\Models\Process;
use App\Models\PartyRate;
use App\Models\WorkerRate;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class AdminProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dimonds = Dimond::where('id', $request->dimonds_id)->first();
        $outerDesignation = Designation::where('category', 'Outter')->pluck('name')->toArray();
        if (in_array($request->designation, $outerDesignation)) {
            $dimonds->update(['status' => 'OutterProcessing']);
        } else {
            $dimonds->update(['status' => 'Processing']);
        }
        Process::create($request->all());


        // Daily::create([
        //     'dimonds_id' => $dimonds->id,
        //     'barcode' => $dimonds->barcode_number,
        //     'stage' => 'issue',
        //     'status' => 1,
        // ]);
        return redirect('admin/dimond/show/' . $request->dimonds_barcode)->with('success', "Save Record Successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $process = Process::where('id', $id)->first();
        return response()->json(['data' => $process]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request)
    {
        $process = Process::where('id', $request->id)->first();
        $dimonds = Dimond::where('id', $process->dimonds_id)->first();
        $r_weight = $request->return_weight;
        $i_weight = $process->issue_weight;

        $diffrence = $i_weight - $r_weight;
        $weight = $i_weight;
        $designation = Designation::where('name', $process->designation)->first();
        if ($designation->rate_apply_on == 'return_weight' || $designation->rate_apply_on == 'ready_to_ruff_weight') {
            $weight = $r_weight;
        }
        if ($designation->rate_apply_on == 'diff_weight') {
            $weight = $diffrence;
        }

        if ($i_weight < $r_weight) {
            return Redirect::back()->with('error', "Return weight large than Issue weight");
        }

        $rate_cut = $request->has('ratecut') ? (($request->ratecut != null) ? 1 : 0) : 0;
        $getWorker = Worker::where('fname', $process->worker_name)->where('designation', $process->designation)->first();

        if (isset($weight)) {

            if ($dimonds->shape == 'Round') {
                if ($weight < 2)
                    $get_rate = !empty($getWorker->round_1) && $getWorker->round_1 != '' ? $getWorker->round_1 : 0;
                else if ($weight >= 2 && $weight < 5)
                    $get_rate = !empty($getWorker->round_2) && $getWorker->round_2 != '' ? $getWorker->round_2 : 0;
                else
                    $get_rate = !empty($getWorker->round_3) && $getWorker->round_3 != '' ? $getWorker->round_3 : 0;
            }

            if ($dimonds->shape != 'Round') {
                if ($weight < 0.99)
                    $get_rate = !empty($getWorker->fancy_0) && $getWorker->fancy_0 != '' ? $getWorker->fancy_0 : 0;
                else if ($weight >= 1 && $weight < 1.5)
                    $get_rate = !empty($getWorker->fancy_1) && $getWorker->fancy_1 != '' ? $getWorker->fancy_1 : 0;
                else if ($weight >= 1.5 && $weight < 2)
                    $get_rate = !empty($getWorker->fancy_2) && $getWorker->fancy_2 != '' ? $getWorker->fancy_2 : 0;
                else if ($weight >= 2 && $weight < 3)
                    $get_rate = !empty($getWorker->fancy_3) && $getWorker->fancy_3 != '' ? $getWorker->fancy_3 : 0;
                else if ($weight >= 3 && $weight < 4)
                    $get_rate = !empty($getWorker->fancy_4) && $getWorker->fancy_4 != '' ? $getWorker->fancy_4 : 0;
                else if ($weight >= 4 && $weight < 5)
                    $get_rate = !empty($getWorker->fancy_5) && $getWorker->fancy_5 != '' ? $getWorker->fancy_5 : 0;
                else if ($weight >= 5 && $weight < 9)
                    $get_rate = !empty($getWorker->fancy_6) && $getWorker->fancy_6 != '' ? $getWorker->fancy_6 : 0;
                else
                    $get_rate = !empty($getWorker->fancy_7) && $getWorker->fancy_7 != '' ? $getWorker->fancy_7 : 0;
            }

            // if ($weight < 1.5)
            //     $key = 'key_1';
            // else if ($weight >= 1.5 && $weight < 2)
            //     $key = 'key_2';
            // else if ($weight >= 2 && $weight < 3)
            //     $key = 'key_3';
            // else
            //     $key = 'key_4';

            // $get_rate = WorkerRate::where('designation', $process->designation)->where('Key', $key)->first();

            if (isset($get_rate)) {
                $countprocess = Process::where(['dimonds_id' => $process->dimonds_id, 'designation' => $process->designation])->where('return_weight', '!=', '')->count();
                $processid = Process::where(['dimonds_id' => $process->dimonds_id, 'designation' => $process->designation])->where('return_weight', '!=', '')->first();

                $getpdata = Process::where(['dimonds_id' => $process->dimonds_id, 'designation' => $process->designation])->get();

                $datas = $getpdata->pluck('id');

                $previousId = '';
                $previousdata = '';

                $currentIdIndex = $datas->search($request->id);

                if ($currentIdIndex !== false && $currentIdIndex > 0) {
                    $previousId = $datas->get($currentIdIndex - 1);
                }

                if (!empty($previousId)) {
                    $previousdata = Process::where(['id' => $previousId])->first();
                }

                if ($designation->rate_apply_on == 'ready_to_ruff_weight') {
                    $getFirstProcess = Process::where('dimonds_id', $process->dimonds_id)->where('designation', $process->designation)->first();
                    $weight = $getFirstProcess->issue_weight;
                }

                if ($rate_cut == 1) {
                    $request['price'] = 0;
                    Process::where(['dimonds_barcode' => $process->dimonds_barcode, 'worker_name' => $process->worker_name])->update(['ratecut' => 1]);
                } elseif ($countprocess == 0) {
                    $request['price'] = $weight * ($get_rate);
                } else {
                    if ($processid->id == $request->id) {
                        $request['price'] = $weight * ($get_rate);
                    } else {
                        if (!empty($previousdata) && $previousdata->price == 0 && $previousdata->ratecut == 1) {
                            $request['price'] = $weight * ($get_rate);
                        } else {
                            if ($designation->rate_apply_on == 'diff_weight') {
                                $request['price'] = $weight * ($get_rate);
                            } else {
                                $request['price'] = 0;
                            }
                        }
                    }
                }

                // if ($rate_cut == 1) {
                //     $request['price'] = 0;
                // } elseif ($countprocess == 0) {
                //     $request['price'] = $weight * ($get_rate->value);
                // } else {
                //     if ($processid->id == $request->id) {
                //         $request['price'] = $weight * ($get_rate->value);
                //     } else {
                //         if (!empty($previousdata) && $previousdata->price == 0) {
                //             $request['price'] = $weight * ($get_rate->value);
                //         } else {
                //             $request['price'] = 0;
                //         }
                //     }
                // }
            }
        }

        $check_process = Process::where(['dimonds_id' => $process->dimonds_id])->where('return_weight', '!=', '')->count();
        if ($check_process == 0) {
            $dimonds->update(['status' => 'Processing']);
        }
        if ($process->designation == 'Grading' && isset($r_weight)) {

            $partyrate = Party::where('id', $dimonds->parties_id)->first();

            // $takenWeight = $dimonds->weight; // old condition
            $takenWeight = $r_weight;

            if ($dimonds->shape == 'Round') {
                if ($takenWeight < 3)
                    $get_party_rate = !empty($partyrate->round_1) && $partyrate->round_1 != '' ? $partyrate->round_1 : 0;
                else if ($takenWeight >= 3 && $takenWeight < 10)
                    $get_party_rate = !empty($partyrate->round_2) && $partyrate->round_2 != '' ? $partyrate->round_2 : 0;
                else
                    $get_party_rate = !empty($partyrate->round_3) && $partyrate->round_3 != '' ? $partyrate->round_3 : 0;
            }

            if ($dimonds->shape != 'Round') {
                if ($takenWeight < 0.99)
                    $get_party_rate = !empty($partyrate->fancy_1) && $partyrate->fancy_1 != '' ? $partyrate->fancy_1 : 0;
                else if ($takenWeight >= 1 && $takenWeight < 1.5)
                    $get_party_rate = !empty($partyrate->fancy_2) && $partyrate->fancy_2 != '' ? $partyrate->fancy_2 : 0;
                else if ($takenWeight >= 1.5 && $takenWeight < 2)
                    $get_party_rate = !empty($partyrate->fancy_3) && $partyrate->fancy_3 != '' ? $partyrate->fancy_3 : 0;
                else if ($takenWeight >= 2 && $takenWeight < 3)
                    $get_party_rate = !empty($partyrate->fancy_4) && $partyrate->fancy_4 != '' ? $partyrate->fancy_4 : 0;
                else if ($takenWeight >= 3 && $takenWeight < 4)
                    $get_party_rate = !empty($partyrate->fancy_5) && $partyrate->fancy_5 != '' ? $partyrate->fancy_5 : 0;
                else if ($takenWeight >= 4 && $takenWeight < 5)
                    $get_party_rate = !empty($partyrate->fancy_6) && $partyrate->fancy_6 != '' ? $partyrate->fancy_6 : 0;
                else if ($takenWeight >= 5 && $takenWeight < 9)
                    $get_party_rate = !empty($partyrate->fancy_7) && $partyrate->fancy_7 != '' ? $partyrate->fancy_7 : 0;
                else
                    $get_party_rate = !empty($partyrate->fancy_8) && $partyrate->fancy_8 != '' ? $partyrate->fancy_8 : 0;
            }

            if (isset($get_party_rate))
                $dimond_amount = ($dimonds->weight) * ($get_party_rate);

            $dimonds->update(['status' => 'Completed', 'amount' => $dimond_amount]);

            $daily = Daily::where('dimonds_id', $process->dimonds_id)->first();
            isset($daily) ? $daily->delete() : '';
        }

        $requestData = $request->all();
        $requestData['ratecut'] = $rate_cut;

        $process->update($requestData);

        $check_process = Process::where(['dimonds_id' => $process->dimonds_id])->where('return_weight', '==', '')->count();
        if ($check_process == 0 && isset($r_weight) && $process->designation != 'Grading') {
            $dimonds->update(['status' => 'Processing']);
        }

        return redirect('admin/dimond/show/' . $request->dimonds_barcode)->with('success', "Update Record Successfully");
    }


    // public function update(Request $request)
    // {
    //     $process = Process::where('id', $request->id)->first();
    //     $dimonds = Dimond::where('id', $process->dimonds_id)->first();
    //     $r_weight = $request->return_weight;
    //     $i_weight = $process->issue_weight;

    //     $diffrence = $i_weight - $r_weight;
    //     $weight = $i_weight;
    //     $designation = Designation::where('name', $process->designation)->first();
    //     if ($designation->rate_apply_on == 'return_weight') {
    //         $weight = $r_weight;
    //     }
    //     if ($designation->rate_apply_on == 'diff_weight') {
    //         $weight = $diffrence;
    //     }

    //     if ($i_weight < $r_weight) {
    //         return Redirect::back()->with('error', "Return weight large than Issue weight");
    //     }
    //     $rate_cut = $request->has('ratecut') ? (($request->ratecut != null) ? 1 : 0) : 0;
    //     if (isset($weight)) {
    //         if ($weight < 1.5)
    //             $key = 'key_1';
    //         else if ($weight >= 1.5 && $weight < 2)
    //             $key = 'key_2';
    //         else if ($weight >= 2 && $weight < 3)
    //             $key = 'key_3';
    //         else
    //             $key = 'key_4';

    //         $get_rate = WorkerRate::where('designation', $process->designation)->where('Key', $key)->first();
    //         if (isset($get_rate)) {
    //             $countprocess = Process::where(['dimonds_id' => $process->dimonds_id, 'designation' => $process->designation])->where('return_weight', '!=', '')->count();
    //             $processid = Process::where(['dimonds_id' => $process->dimonds_id, 'designation' => $process->designation])->where('return_weight', '!=', '')->first();
    //             if ($rate_cut == 1) {
    //                 $request['price'] = 0;
    //             } elseif ($countprocess == 0) {
    //                 $request['price'] = $weight * ($get_rate->value);
    //             } else {
    //                 if ($processid->id == $request->id) {
    //                     $request['price'] = $weight * ($get_rate->value);
    //                 } else {
    //                     $request['price'] = 0;
    //                 }
    //             }
    //             // if ($countprocess == 0) {
    //             //     $request['price'] = $rate_cut == 1 ? 0 : ($i_weight * ($get_rate->value));
    //             // } elseif ($rate_cut == 0) {
    //             //     $request['price'] = $i_weight * ($get_rate->value);
    //             // } else {
    //             //     $request['price'] = 0;
    //             // }
    //         }
    //     }

    //     $check_process = Process::where(['dimonds_id' => $process->dimonds_id])->where('return_weight', '!=', '')->count();
    //     if ($check_process == 0) {
    //         $dimonds->update(['status' => 'Processing']);
    //     }
    //     if ($process->designation == 'Grading' && isset($r_weight)) {

    //         if ($dimonds->weight < 1.5)
    //             $key = 'key_1';
    //         else if ($dimonds->weight >= 1.5 && $dimonds->weight < 2)
    //             $key = 'key_2';
    //         else if ($dimonds->weight >= 2 && $dimonds->weight < 3)
    //             $key = 'key_3';
    //         else
    //             $key = 'key_4';

    //         $get_party_rate = PartyRate::where('Key', $key)->first();
    //         if (isset($get_party_rate))
    //             $dimond_amount = ($dimonds->weight) * ($get_party_rate->value);

    //         $dimonds->update(['status' => 'Completed', 'amount' => $dimond_amount]);

    //         $daily = Daily::where('dimonds_id', $process->dimonds_id)->first();
    //         isset($daily) ? $daily->delete() : '';
    //     }

    //     $requestData = $request->all();
    //     $requestData['ratecut'] = $rate_cut;

    //     $process->update($requestData);

    //     $check_process = Process::where(['dimonds_id' => $process->dimonds_id])->where('return_weight', '==', '')->count();
    //     if ($check_process == 0 && isset($r_weight) && $process->designation != 'Grading') {
    //         $dimonds->update(['status' => 'Processing']);
    //     }

    //     return redirect('admin/dimond/show/' . $request->dimonds_barcode)->with('success', "Update Record Successfully");
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $process = Process::findOrFail($id);
        $count = Process::where('dimonds_id', $process->dimonds_id)->count();

        if ($count == 1) {
            $dimonds = Dimond::where('id', $process->dimonds_id)->first();
            $dimonds->update(['status' => 'Pending']);
        } else {
            $dimonds = Dimond::where('id', $process->dimonds_id)->first();
            if ($process->designation == 'Grading' && $dimonds->status == 'Completed') {
                $dimonds->update(['status' => 'Processing']);
            }
        }

        $process->delete();
        // $daily = Daily::where('dimonds_id', $process->dimonds_id)->first();
        // $daily->delete();
        return Redirect::back()->with('success', "Deleted Record Successfully");
    }
}
