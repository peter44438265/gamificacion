<?php

/**
 * @author  Laurynas Karvelis <laurynas.karvelis@gmail.com>
 * @author  Explosive Brains Limited
 * @license http://sam.zoy.org/wtfpl/COPYING
 */
class Facebook_Api extends Facebook_Api_Base
{
    /**
     * Returns user's profile metadata
     *
     * @param void
     * @return array
     */
    public function getProfile()
    {
        return $this->apiCall('/me');
    }

    /**
     * Posts on user's wall
     *
     * @param string      $username
     * @param string|null $message
     * @param string|null $link
     * @param string|null $picture
     * @param string|null $name
     * @param string|null $caption
     * @param string|null $description
     * @return string|bool post Id or false on failure
     */
    public function postOnWall(
        $username,
        $message = null,
        $link = null,
        $picture = null,
        $name = null,
        $caption = null,
        $description = null
    )
    {
        $username = (string) $username;

        $post = array();

        if (!is_null($message)) {
            $post['message'] = (string) $message;
        }
        if (!is_null($link)) {
            $post['link'] = (string) $link;
        }
        if (!is_null($picture)) {
            $post['picture'] = (string) $picture;
        }
        if (!is_null($name)) {
            $post['name'] = (string) $name;
        }
        if (!is_null($caption)) {
            $post['caption'] = (string) $caption;
        }
        if (!is_null($description)) {
            $post['description'] = (string) $description;
        }

        try {
            // third parameter indicates that request will be POST instead of normal GET
            return $this->apiCall('/' . $username . '/feed', $post, true);
        } catch (Exception $e) {
            return false;
        }
    }
}