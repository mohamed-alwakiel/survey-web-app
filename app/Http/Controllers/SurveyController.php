<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Http\Resources\SurveyResource;

use App\Models\Survey;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        return SurveyResource::collection(Survey::where('user_id', $user->id)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSurveyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSurveyRequest $request)
    {
        $data = $request->validated();

        // check if image was given and save on local file system
        if (isset($data['image'])) :
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;
        endif;

        $survey = Survey::create($data);
        return new SurveyResource($survey);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function show(Survey $survey, Request $request)
    {
        $user = $request->user();
        if ($user->id !== $survey->user_id) :
            return abort(403, 'Unauthorized action');
        endif;

        return new SurveyResource($survey);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSurveyRequest  $request
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSurveyRequest $request, Survey $survey)
    {
        $data = $request->validated();

        // check if image was given and save on local file system
        if (isset($data['image'])) :
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;

            // if there is an old image, delete it
            if ($survey->image) :
                $absolutePath = public_path($survey->image);
                File::delete($absolutePath);
            endif;
        endif;

        $survey->update($data);
        return new SurveyResource($survey);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Survey $survey, Request $request)
    {
        $user = $request->user();
        if ($user->id !== $survey->user_id) :
            return abort(403, 'Unauthorized action');
        endif;

        $survey->delete();

        // if there is an old image, delete it
        if ($survey->image) :
            $absolutePath = public_path($survey->image);
            File::delete($absolutePath);
        endif;

        return response('delete', 204);
    }


    private function saveImage($image)
    {

        // check if image is valid base64 string
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) :
            // Tack out the base64 encoded text without mime type
            $image = substr($image, strpos($image, ',') + 1);

            // get file extension
            $type = strtolower($type[1]); // jpg, png, gif

            // check if file is an image
            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) :
                throw new \Exception('Invalid image type');
            endif;

            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            if ($image === false) :
                throw new \Exception('base64_decode failed');
            endif;

        else :
            throw new \Exception('did not match data URI with image data');
        endif;

        $dir = 'images/';
        $file = Str::random() . '.' . $type;

        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;

        if (!File::exists($absolutePath)) :
            File::makeDirectory($absolutePath, 0755, true);
        endif;

        file_put_contents($relativePath, $image);

        return $relativePath;
    }
}
