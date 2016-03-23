<?
namespace JV;

class CardsService
{
  private $data;

  function __construct()
  {
    try {
      $json = file_get_contents(dirname(__FILE__) . '/cards.json');
      $this->data = json_decode($json);
      if (!$this->data) {
        die('Empty JSON');
      }
    }
    catch (Exception $e)
    {
      die('Error loading JSON');
    }
  }

  public function getSections()
  {
    return $this->data->sections;
  }

  public function getSection($section_slug)
  {
    $d = $this->data->sections->$section_slug;
    return ($d ? $d : 'No such section');
  }

  public function getCards()
  {
    return $this->data->cards;
  }

  public function getCard($card_slug)
  {
    $d = $this->data->cards->$card_slug;
    return ($d ? $d : 'No such card');
  }

  public function searchCards($term)
  {
    $title_results = array();
    $body_results = array();

    if (strlen($term) < 3) {
      return 'Error: please enter at least 3 characters';
    }

    foreach ($this->data->cards as $card) {
      $pattern = '/('.$term.')/i';
      preg_match_all($pattern, $card->title, $matches);
      if (count($matches[0]) > 0) {
        $title_results[] = $card;
      } else {
        $pattern = '/(\W+'.$term.')/i';
        preg_match_all($pattern, $card->markdown, $matches);
        if (count($matches[0]) > 0) {
          $body_results[] = $card;
        }
      }
    }

    if (count($title_results) == 0 || count($body_results) == 0) {
      return 'Error: no results found';
    }
    
    return array('title_results' => $title_results, 'body_results' => $body_results);
  }

}