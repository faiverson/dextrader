<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\TrainingUpdateValidator;
use App\Services\TrainingCreateValidator;
use App\Repositories\TrainingRepository;

class TrainingGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(TrainingRepository $repository, TrainingCreateValidator $createValidator, TrainingUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}

	public function getCertification($userId)
	{
		return $this->repository->getCertification($userId);
	}

	public function parse_youtube($link){

		$regexstr = '~
            # Match Youtube link and embed code
            (?:                             # Group to match embed codes
                (?:<iframe [^>]*src=")?       # If iframe match up to first quote of src
                |(?:                        # Group to match if older embed
                    (?:<object .*>)?      # Match opening Object tag
                    (?:<param .*</param>)*  # Match all param tags
                    (?:<embed [^>]*src=")?  # Match embed tag to the first quote of src
                )?                          # End older embed code group
            )?                              # End embed code groups
            (?:                             # Group youtube url
                https?:\/\/                 # Either http or https
                (?:[\w]+\.)*                # Optional subdomains
                (?:                         # Group host alternatives.
                youtu\.be/                  # Either youtu.be,
                | youtube\.com              # or youtube.com
                | youtube-nocookie\.com     # or youtube-nocookie.com
                )                           # End Host Group
                (?:\S*[^\w\-\s])?           # Extra stuff up to VIDEO_ID
                ([\w\-]{11})                # $1: VIDEO_ID is numeric
                [^\s]*                      # Not a space
            )                               # End group
            "?                              # Match end quote if part of src
            (?:[^>]*>)?                       # Match any extra stuff up to close brace
            (?:                             # Group to match last embed code
                </iframe>                 # Match the end of the iframe
                |</embed></object>          # or Match the end of the older embed
            )?                              # End Group of last bit of embed code
            ~ix';

		preg_match($regexstr, $link, $matches);
		return $matches ? $matches[1] : $link;
	}

	public function checkpoint($training_id, $userId, $type)
	{
		// since there isn't support in Eloquent to
		// composite primary keys, I decided to use
		// query builder instead
		$isOne = $this->repository->getUserTraining($training_id, $userId);
		if($isOne > 0) {
			$this->errors = ['The training is already completed'];
			return false;
		}
		return $this->repository->addUserTraining($training_id, $userId, $type);
	}

	public function getTotalTrainings($type)
	{
		return $this->repository->getTotalTrainings($type);
	}

	public function getTrainingCompletedByUser($type, $userId)
	{
		return $this->repository->getTrainingCompletedByUser($type, $userId);
	}

	public function add($data)
	{
		if( ! $this->createValidator->with($data)->passes() )
		{
			$this->errors = $this->createValidator->errors();
			return false;
		}

		if(!array_key_exists('list_order', $data) || (array_key_exists('list_order', $data) && $data['list_order'] != '')) {
			$position = $this->findTotalByType($data['type']);
			$data['list_order'] = $position + 1;
		}

		return $this->repository->create($data);
	}

	public function findTotalByType($type)
	{
		return $this->repository->findTotalByType($type);
	}

}