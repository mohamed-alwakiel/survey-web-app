<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Http\Resources\SurveyResource;

use App\Models\Survey;
use App\Models\SurveyQuestion;

use App\Enums\QuestionType;
use App\Http\Requests\StoreSurveyAnswerRequest;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestionAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Support\Arr;

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
        return SurveyResource::collection(Survey::where('user_id', $user->id)->paginate(5));
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

        // create new questions
        foreach ($data['questions'] as $question) :
            $question['survey_id'] = $survey->id;
            $this->createQuestion($question);
        endforeach;

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
     * Display the specified resource.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function showForGuest(Survey $survey)
    {
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

        // Get ids as plain array of existing questions
        $existingIDs = $survey->questions()->pluck('id')->toArray();
        // Get ids as plain array of new questions
        $newIDs = Arr::pluck($data['questions'], 'id');

        // Find questions to delete
        $toDelete = array_diff($existingIDs, $newIDs);
        //Find questions to add
        $toAdd = array_diff($newIDs, $existingIDs);

        // Delete questions by $toDelete array
        SurveyQuestion::destroy($toDelete);

        // Create new questions
        foreach ($data['questions'] as $question) :
            if (in_array($question['id'], $toAdd)) :
                $question['survey_id'] = $survey->id;
                $this->createQuestion($question);
            endif;
        endforeach;

        // Update existing questions
        $questionMap = collect($data['questions'])->keyBy('id');
        foreach ($survey->questions as $question) :
            if (isset($questionMap[$question->id])) :
                $this->updateQuestion($question, $questionMap[$question->id]);
            endif;
        endforeach;

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


    /**
     * Save image in local file system and return saved image path
     *
     * @param $image
     * @throws \Exception
     */
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


    /**
     * Create a question and return
     *
     * @param $data
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    private function createQuestion($question)
    {
        if (is_array($question['data'])) :
            $question['data'] = json_encode($question['data']);
        endif;

        $data = Validator::make($question, [
            'question' => 'required|string',
            'type' => [new EnumValue(QuestionType::class)],
            'description' => 'nullable|string',
            'data' => 'present',
            'survey_id' => 'exists:App\Models\Survey,id'
        ]);

        return SurveyQuestion::create($data->validated());
    }

    /**
     * Update a question and return true or false
     *
     * @param \App\Models\SurveyQuestion $surveyQuestion
     * @param                            $question
     * @return bool
     * @throws \Illuminate\Validation\ValidationException
     */
    private function updateQuestion(SurveyQuestion $surveyQuestion, $question)
    {
        if (is_array($question['data'])) :
            $question['data'] = json_encode($question['data']);
        endif;

        $data = Validator::make($question, [
            'id' => 'exists:App\Models\SurveyQuestion,id',
            'question' => 'required|string',
            'type' => [new EnumValue(QuestionType::class)],
            'description' => 'nullable|string',
            'data' => 'present',
        ]);

        return $surveyQuestion->update($data->validated());
    }


    /**
     * Store a survey answers.
     *
     * @param  \App\Http\Requests\StoreSurveyAnswerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAnswer(StoreSurveyAnswerRequest $request, Survey $survey)
    {
        $data = $request->validated();

        $surveyAnswer = SurveyAnswer::create([
            'survey_id' => $survey->id,
            'start_date' => date('Y-m-d H:i:s'),
            'end_date' => date('Y-m-d H:i:s'),
        ]);

        foreach ($data['answers'] as $questionId => $answer) :
            $question = SurveyQuestion::where(['id' => $questionId, 'survey_id' => $survey->id])->get();

            if (!$question) :
                return response('Invalid question ID :' . $questionId, 400);
            endif;

            $answers = [
                'survey_question_id' => $questionId,
                'survey_answer_id' => $surveyAnswer->id,
                'answer' => is_array($answer) ? json_encode($answer) : $answer,
            ];

            $questionsAnswer = SurveyQuestionAnswer::create($answers);

        endforeach;

        return response("", 201);
    }
}
