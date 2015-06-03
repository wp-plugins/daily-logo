<?php
/**
 * Class Daily_Logo
 *
 * Gets the logo info object
 */

class Daily_Logo {

	/**
	 * Logo ID
	 *
	 * @var int @id
	 */
	public $id;

	/**
	 * Blog ID
	 *
	 * @var int @blog_id
	 */
	public $blog_id;

	/**
	 * Logo name (used also for title and alt text)
	 *
	 * @var string $name
	 */
	public $name;

	/**
	 * Logo year
	 *
	 * @var int $year
	 */
	public $year;

	/**
	 * Logo month
	 *
	 * @var int $month
	 */
	public $month;

	/**
	 * Logo day
	 *
	 * @var int $day
	 */
	public $day;

	/**
	 * Logo link
	 *
	 * @var string $link
	 */
	public $link;

	/**
	 * Logo link target
	 *
	 * @var int $target
	 */
	public $target;

	/**
	 * Logo image
	 *
	 * @var string $image
	 */
	public $image;

	/**
	 * Logo alternative image
	 *
	 * @var string $image_alternative
	 */
	public $image_alternative;

	/**
	 * Logo CSS class
	 *
	 * @var string $class
	 */
	public $class;

	/**
	 * __construct from result set
	 *
	 * @param $rs
	 */
	public function __construct( $rs ) {
		$this->id = (int) $rs->id;
		$this->blog_id = (int) $rs->blog_id;
		$this->name = trim( strip_shortcodes( strip_tags( (string) $rs->logo_name ) ) );
		$this->year = (int) $rs->logo_year;
		$this->month = (int) $rs->logo_month;
		$this->day = (int) $rs->logo_day;
		$this->link = (string) $rs->logo_link;
		$this->target = (int) $rs->logo_target;
		$this->image = (string) $rs->logo_image;
		$this->image_alternative = (string) $rs->logo_image_alternative;
		$this->class = (string) $rs->logo_class;
	}
} 