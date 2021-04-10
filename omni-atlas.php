<?php

 add_post_type_support( 'page', 'excerpt' );

function clean_image_code($content)
{
	global $post;
	/* $pattern = '/(?:<div class=\"wp-block-image\">)?<figure class=\"(aligncenter|alignleft|alignright)?[A-Za-z- ]*\"><img(?:.+?)src="(.+?)(\.[^\."]+)"(.*?)class=\".*?\"(.*?)>(<figcaption>.*?<\/figcaption>)?<\/figure>(?:<\/div>)?/i';
	$replacement   = '<figure class="content-figure $1"><picture><source srcset="$2.webp" type="image/webp"><source srcset="$2.jpg" type="image/jpeg"><img loading="lazy" src="$2$3" class="content-image" $4$5></picture>$6</figure>';
	$content = preg_replace($pattern, $replacement, $content); */

	$pattern = '/(?:<div class=\"wp-block-image\">)?<figure class=\"(aligncenter|alignleft|alignright)?[A-Za-z- ]*\"><img loading="lazy" width="(.+?)" height="(.+?)" src="(.+?)(\.[^\."]+)"(.*?)class=\".*?\"(.*?)>(<figcaption>.*?<\/figcaption>)?<\/figure>(?:<\/div>)?/i';
	$replacement   = '<figure class="content-figure $1"><picture style="width:$2px;height:$3px"><source srcset="$4.webp" type="image/webp"><source srcset="$4.jpg" type="image/jpeg"><img loading="lazy" src="$4$5" class="content-image" $6$7></picture>$8</figure>';
	$content = preg_replace($pattern, $replacement, $content);

	$pattern = '/content\/original\//i';
	$replacement   = 'content/';
	$content       = preg_replace($pattern, $replacement, $content);
	return $content;
}
add_filter('the_content', 'clean_image_code');

/*
 function change_backblaze_url($content) {
    global $post;
    $pattern = '/f003.backblazeb2.com/i';
    $replacement   = 'media.omni-atlas.com';
    $content       = preg_replace($pattern,$replacement,$content);
    return $content;
 }
 add_filter('the_content','change_backblaze_url');*/


function clean_code($content)
{
	$pattern = '/<hr.*>/i';
	$replacement   = '<hr>';
	$content       = preg_replace($pattern, $replacement, $content);

	$pattern = '/<!--.*-->/i';
	$replacement   = '';
	$content       = preg_replace($pattern, $replacement, $content);

	$pattern = '/\n/i';
	$replacement   = '';
	$content       = preg_replace($pattern, $replacement, $content);

	//<PATH> is replaced during build on hydra-aeon
	$pattern = '/href=\"#/i';
	$replacement   = 'href="<PATH>#';
	$content       = preg_replace($pattern, $replacement, $content);

	$site_url = str_replace('/', '\/', get_site_url());

	$pattern = '/' . $site_url . '\/[A-z]+\//i';
	$replacement   = '<BLOG>';
	$content       = preg_replace($pattern, $replacement, $content);

	$pattern = '/class="wp-block-quote"/i';
	$replacement   = '';
	$content       = preg_replace($pattern, $replacement, $content);

	$pattern = '/has-text-align-center/i';
	$replacement   = 'text-align-center';
	$content       = preg_replace($pattern, $replacement, $content);

	return $content;
}
add_filter('the_content', 'clean_code');

function category_desc_filter( $content ) {
	$site_url = str_replace('/', '\/', get_site_url());
	$pattern = '/' . $site_url . '\/[A-z]+\//i';
	$replacement   = '<BLOG>';
	$content       = preg_replace($pattern, $replacement, $content);

	$pattern = '/<img class=\"[A-Za-z0-9- ]*(aligncenter|alignleft|alignright)\" src="(.+?)(\.[^\."]+)" alt="([^"]+)?" width="(.+?)" height="(.+?)"(.*?)>/i';
	$replacement   = '<figure class="content-figure $1"><picture style="width:$5px;height:$6px"><source srcset="$2.webp" type="image/webp"><source srcset="$2.jpg" type="image/jpeg"><img loading="lazy" src="$2$3" class="content-image"$7></picture></figure>	';
	$content = preg_replace($pattern, $replacement, $content);

	$pattern = '/content\/original\//i';
	$replacement   = 'content/';
	$content       = preg_replace($pattern, $replacement, $content);

	return $content;
 }
 add_filter( 'category_description', 'category_desc_filter' );
/*
add_action( 'rest_api_init', function () {
    register_rest_field( 'post', 'all_meta', array(
        'get_callback' => function() {
            
            delete_post_meta(get_the_ID(), 'region');
            
            $all_meta = get_post_meta(get_the_ID());
            
            return $all_meta;
        },
        'update_callback' => function() {
            return true;
        }
    ) );
} );
*/

/*
add_action( 'rest_api_init', function () {
    register_rest_field( 'category', 'all_meta', array(
        'get_callback' => function($category) {
            
            $all_meta = get_term_meta($category['id']);
            
            //delete idk
            
            return $all_meta;
        },
        'update_callback' => function() {
            return true;
        }
    ) );
} );
*/



add_action('rest_api_init', function () {
	register_rest_field('tag', 'atlas', array(
		'get_callback' => function ($tag) {
			//$value['id'] = $tag['id'];

			$tag_data = get_tag($tag['id']);

			$value['title'] = $tag_data->name;
			$value['slug'] = strtolower(str_replace(" ", "-", $value['title']));

			$value['language'] = pll_get_term_language($tag['id']);

			//adding a meta so api can be queried with

			//?filter[meta_key]=slug&filter[meta_value]=<slug_name>
			update_term_meta($tag['id'], 'slug', $value['slug']);

			//?filter[meta_key]=region&filter[meta_value]=<region_code>
			update_term_meta($tag['id'], 'language', $value['language']);

			$translations = pll_get_term_translations($tag['id']);

			foreach ($translations as $language => $tag_id) {
				$lang_tag_data = get_tag($tag_id);
				$tag_slug = strtolower(str_replace(" ", "-", $lang_tag_data->name));
				$value['hreflangs'][$language] = $tag_slug;
				if($language=="en"){
					update_term_meta($tag['id'], 'group', $tag_slug);
					//$value['group'] = get_term_meta($tag['id'], 'group', true);
				}
			}

			$value['meta']['title'] = empty(get_term_meta($tag['id'], '_seopress_titles_title', true)) ? $value['title'] : get_term_meta($tag['id'], '_seopress_titles_title', true);

			$value['meta']['description'] = get_term_meta($tag['id'], '_seopress_titles_desc', true);

			$value['content'] = tag_description($tag['id']);

			return $value;
		},
		'update_callback' => function () {
			return true;
		}
	));
});


add_action('rest_api_init', function () {
	register_rest_field('category', 'atlas', array(
		'get_callback' => function ($category) {
			//$category_data = get_category($category['id']);
			//if any additional data is required, use as:
			//$category_data->property_name

			//$value['id'] = $category['id'];

			$value['title'] = get_cat_name($category['id']);

			//using name as slug in order to use same slug in different languages
			$value['slug'] = strtolower(str_replace(" ", "-", $value['title']));

			$value['language'] = pll_get_term_language($category['id']);

			//delete_term_meta($category['id'], 'name');

			//adding a meta so api can be queried with
			//?filter[meta_key]=slug&filter[meta_value]=<slug>
			update_term_meta($category['id'], 'slug', $value['slug']);

			//?filter[meta_key]=region&filter[meta_value]=<region_code>
			update_term_meta($category['id'], 'language', $value['language']);

			$translations = pll_get_term_translations($category['id']);

			foreach ($translations as $language => $category_id) {
				$lang_category_data = get_category($category_id);
				$value['hreflangs'][$language] = strtolower(str_replace(" ", "-", $lang_category_data->name));
			}

			$value['time']['published'] = get_option('gmt_offset') == 0 ? subStr(get_the_date('c', $category['id']), 0, 19)."Z" : get_the_date('c', $category['id']); 
			$value['time']['modified'] = get_option('gmt_offset') == 0 ? subStr(get_the_modified_date('c', $category['id']), 0, 19)."Z" : get_the_modified_date('c', $category['id']); 

			$value['meta']['title'] = empty(get_term_meta($category['id'], '_seopress_titles_title', true)) ? $value['title'] : get_term_meta($category['id'], '_seopress_titles_title', true);

			$value['meta']['description'] = get_term_meta($category['id'], '_seopress_titles_desc', true);

			$value['meta']['facebook']['img'] = get_term_meta($category['id'], '_seopress_social_fb_img', true);

			$value['meta']['twitter']['img'] = get_term_meta($category['id'], '_seopress_social_twitter_img', true);

			$value['content'] = category_description($category['id']);

			return $value;
		},
		'update_callback' => function () {
			return true;
		}
	));
});


add_action('rest_api_init', function () {
	register_rest_field('page', 'atlas', array(
		'get_callback' => function ($page) {

			//$value["data"] = $page;
			
			$children = get_children(array(
				'post_parent' => $page['id'],
				'post_type'   => 'page',
				'post_status' => 'publish')
			);

			if(!empty($children)){
				$value["children"] = [];
				$i = 0;
				foreach ($children as $child){
					$value['children'][$i] = ['name' => $child->post_title, 'slug' => strtolower(str_replace(" ", "-", $child->post_title))];
					$i++;
				}
			}

			$value['title'] = get_the_title($page['id']);
			$value['excerpt'] = get_the_excerpt($page['id']);

			$image = get_the_post_thumbnail_url($page['id']);

			if (!empty($image)) {
				$image = pathinfo($image);
				$value['image'] = $image['dirname'] . "/" . $image['filename'];
			};

			$value["slug"] = strtolower(str_replace(" ", "-", $value['title']));
			
			//$value['language'] = pll_get_post_language($page['id']);

			//?filter[meta_key]=slug&filter[meta_value]=<slug_name>
			update_post_meta($page['id'], 'slug', $value['slug']);

			//?filter[meta_key]=region&filter[meta_value]=<region_code>
			update_post_meta($page['id'], 'language', pll_get_post_language($page['id']));

			//SET HREFLANGS FIELD
			$translations = pll_get_post_translations($page['id']);

			foreach ($translations as $language => $page_id) {
				$lang_urls[$language] = strtolower(str_replace(" ", "-", get_the_title($page_id)));
				if($language == "en"){
					//find page based on mutual slug — let's put this on hold
					$value['nativeSlug'] = $lang_urls[$language];
					update_post_meta($page['id'], 'nativeSlug', $lang_urls[$language]);
				}

			}
			$value['hreflangs'] = $lang_urls;

			//THE REST...
			$value['meta']['title'] = empty(get_post_meta($page['id'], '_seopress_titles_title', true)) ? $value['title'] : get_post_meta($page['id'], '_seopress_titles_title', true);

			$value['meta']['description'] = get_post_meta($page['id'], '_seopress_titles_desc', true);

			$value['meta']['facebook']['img'] = get_post_meta($page['id'], '_seopress_social_fb_img', true);

			$value['meta']['twitter']['img'] = get_post_meta($page['id'], '_seopress_social_twitter_img', true);

			$value['content'] =  apply_filters('the_content', get_the_content($page['id']));

			return $value;
		},
		'update_callback' => function () {
			return true;
		}
	));
});


add_action('rest_api_init', function () {
	register_rest_field('post', 'atlas', array(
		'get_callback' => function ($post) {
			//REMOVE / DELETE META
			//delete_post_meta(get_the_ID(), 'rank_math_description');

			//$value['id'] = $post['id'];
			$value['title'] = get_the_title($post['id']);

			$value['excerpt'] = get_the_excerpt($post['id']);
			$value['author'] = get_the_author_meta('display_name', get_post_field('post_author', $post['id']));

			$value['time']['published'] = get_option('gmt_offset') == 0 ? subStr(get_the_date('c', $post['id']), 0, 19)."Z" : get_the_date('c', $post['id']); 
			$value['time']['modified'] = get_option('gmt_offset') == 0 ? subStr(get_the_modified_date('c', $post['id']), 0, 19)."Z" : get_the_modified_date('c', $post['id']); 

			$image = pathinfo(get_the_post_thumbnail_url($post['id']));
			if (!empty($image)) {
				$value['image'] = str_replace("original/", "", $image['dirname'] . "/" . $image['filename']) ;
			};

			$value['slug'] = get_post_field('post_name', $post['id']);

			//adding a meta so api can be queried with
			//?filter[meta_key]=slug&filter[meta_value]=post-slug
			update_post_meta($post['id'], 'slug', $value['slug']);

			//?filter[meta_key]=region&filter[meta_value]=en
			update_post_meta($post['id'], 'language', pll_get_post_language($post['id']));

			//$value['language'] = get_post_meta($post['id'], 'language', true); no need.

			//SET HREFLANGS FIELD
			$translations = pll_get_post_translations($post['id']);

			
			foreach ($translations as $language => $post_id) {
				if(str_contains(get_the_title($post_id), "Private:")){
					continue;
				}
				$lang_urls[$language] =  strtolower(str_replace(" ", "-", get_the_title($post_id)));
			}
			$value['hreflangs'] = $lang_urls;


			//SET CATEGORIES AND POSTS FIELDS
			$post_categories = wp_get_post_categories($post['id']);

			foreach ($post_categories as $index => $category_id) {
				//$value['categories'][$index]['id'] = $category_id;
				$value['categories'][$index] = get_cat_name($category_id);
			}

			update_post_meta($post['id'], 'categories', $value['categories']);

			$post_tags = wp_get_post_tags($post['id']);

			foreach ($post_tags as $index => $tag_obj) {
				$tag_data = get_tag($tag_obj);
				//$value['tag_obj'] = $tag_data;
				$value['tags'][$index] = $tag_data->name;

				//set group to english tag
				$translations = pll_get_term_translations($tag_data->term_id);

				$lang_tag_data = get_tag($translations["en"]);
				$tag_slug = strtolower(str_replace(" ", "-", $lang_tag_data->name));

				$value['group'] = $tag_slug;
				update_post_meta($post['id'], 'group', $tag_slug);
			}

			update_post_meta($post['id'], 'tags', $value['tags']);

			$pillar = get_post_meta($post['id'], 'cardinal', true);
			if(!empty($pillar)){
				$value["cardinal"] = $pillar;
			}

			//THE REST...
			$value['meta']['title'] = empty(get_post_meta($post['id'], '_seopress_titles_title', true)) ? $value['title'] : get_post_meta($post['id'], '_seopress_titles_title', true);

			$value['meta']['description'] = get_post_meta($post['id'], '_seopress_titles_desc', true);

			$value['meta']['facebook']['img'] = get_post_meta($post['id'], '_seopress_social_fb_img', true);

			$value['meta']['twitter']['img'] = get_post_meta($post['id'], '_seopress_social_twitter_img', true);

			$value['content'] =  apply_filters('the_content', get_the_content($post['id']));

			return $value;
		},
		'update_callback' => function () {
			return true;
		}
	));
});



/*Current year*/
function current_year_func()
{
	return date("Y");
}
add_shortcode('current_year', 'current_year_func');

add_filter('lang_query', function ($args, $request) {
	if ($city = $request->get_param('lang')) {
		$args['meta_key'] = 'city';
		$args['meta_value'] = $city;
	}
	return $args;
}, 10, 2);



