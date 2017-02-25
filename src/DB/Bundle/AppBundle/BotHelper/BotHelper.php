<?php

namespace DB\Bundle\AppBundle\BotHelper;


use DB\Bundle\CommonBundle\FacebookMassenger\FbBotApp;
use DB\Bundle\CommonBundle\FacebookMassenger\StructuredMessage;
use DB\Bundle\CommonBundle\FacebookMassenger\AccountLinkingElement;
use DB\Bundle\CommonBundle\FacebookMassenger\Message;
use DB\Bundle\CommonBundle\FacebookMassenger\ButtonMessage;
use DB\Bundle\CommonBundle\FacebookMassenger\CardMessage;
use DB\Bundle\CommonBundle\FacebookMassenger\CardCollectionMessage;
use DB\Bundle\AppBundle\Common\Config;
/**
 * Class UserAccount
 *
 * @package DB\Bundle\AppBundle\BotHelper
 */
class BotHelper {
	const VARIABLE_FIRST_NAME = '[$firstName]';
	const VARIABLE_LAST_NAME = '[$lastName]';
	
	const POSTBACK_POSTREACH_MENU 				= 'POSTBACK_POSTREACH_MENU';
	const POSTBACK_POSTREACH_SIGNUP 			= 'POSTBACK_POSTREACH_SIGNUP';
	const POST_REACH_CONFIRM_INVALID 			= 'POST_REACH_CONFIRM_INVALID';
	const POST_REACH_CONFIRM_SUCCESS 			= 'POST_REACH_CONFIRM_SUCCESS';
	
	const POSTBACK_POSTREACH_DEFAULT_STORIES 	= 'POSTBACK_POSTREACH_DEFAULT_STORIES';
	const POSTBACK_POSTREACH_DEFAULT_STORIES_NEXT_PAGE 	= 'POSTBACK_POSTREACH_DEFAULT_STORIES_NEXT_PAGE';
	
	const POSTBACK_POSTREACH_SEARCH_STORIES 	= 'POSTBACK_POSTREACH_SEARCH_STORIES';
	const POSTBACK_POSTREACH_POST_ARTICLE 		= 'POSTBACK_POSTREACH_POST_ARTICLE';
	
	
	/**
	 * This function send the new user detection message
	 * @param FbBotApp $bot
	 * @param array $commandDetail
	 */
	public static function sendNewUserMessage(FbBotApp $bot, $commandDetail, $options = array()) {
		if(empty($options['authUrl'])) {
			return false;
		}
		
		$message = 'Welcome ' . self::VARIABLE_FIRST_NAME . ' ' . self::VARIABLE_LAST_NAME . ', If you are POST REACH user then signin to confirm your subscription. If you are new user then signup with POST REACH to confirm your subscription';

		$message = new Message($commandDetail['senderId'], self::getMessage($commandDetail, $message));
		$bot->send($message);
		
		$accountLinkingElement = new AccountLinkingElement($options['authUrl'], 'Welcome to postreach, Plesae choose your option.', 'http://dipakpatil.com/dev/postreach/web/bundles/dbapp/postreach/images/xpostreachlogo.png.pagespeed.ic.BVWEvpCC18.png');
		
		$payload = array('operation'=>self::POSTBACK_POSTREACH_SIGNUP, 'data'=>array());
		
		$accountLinkingElement->addSignupButton('Sign Up', json_encode($payload));
		
		$structuredMessage = new StructuredMessage($commandDetail['senderId'], StructuredMessage::TYPE_GENERIC, array('elements'=>array($accountLinkingElement)));
	
		$bot->send($structuredMessage);
	}
	
	/**
	 * This function will return the message by replacing the subscriber detail
	 * @param string $commandDetail
	 * @param string $message
	 */
	public static function getMessage($commandDetail, $message) {
		if(!isset($message)) {
			$message = '';
		}
		
		if(!empty($commandDetail['subscriberDetail'])) {
			foreach($commandDetail['subscriberDetail'] as $key=>$value) {
				$message = str_replace('[$' . $key . ']', $value, $message);
			}
		}
		
		return $message;
	}
	
	/**
	 * This function send welcome menu
	 * @param FbBotApp $bot
	 * @param array $commandDetail
	 * @param string $message
	 */
	public static function sendWelcomeMenu(FbBotApp $bot, $commandDetail, $message = '') {
		if(empty($message)) {
			$message = 'Pick an option below to get going';
		}
		
		$buttonMessage = new ButtonMessage($commandDetail['senderId'], $message);
		$buttonMessage->addPostBackButton('Menu', BotHelper::POSTBACK_POSTREACH_MENU);
			
		$bot->send($buttonMessage->getData());
	}
	
	/**
	 * This function send main menu 
	 * @param FbBotApp $bot
	 * @param array $commandDetail
	 * @param array $options
	 */
	public static function sendMainMenu(FbBotApp $bot, $commandDetail, $options = array()) {
		$message = 'Pick an option below to get going';
		
		$buttonMessage = new ButtonMessage($commandDetail['senderId'], $message);
		$buttonMessage->addPostBackButton('Top Stories', json_encode(array('operation'=>self::POSTBACK_POSTREACH_DEFAULT_STORIES, 'data'=>array('currentPage'=>'1'))));
		$buttonMessage->addPostBackButton('Search Stories', BotHelper::POSTBACK_POSTREACH_SEARCH_STORIES);
			
		$bot->send($buttonMessage->getData());
	}
	
	/**
	 * This function send trending article to users
	 * @param FbBotApp $bot
	 * @param array $commandDetail
	 * @param array $trendingArticleList
	 */
	public static function sendTrendignArticle(FbBotApp $bot, $commandDetail, $trendingArticleList, $options = array()) {
		if(empty($trendingArticleList['LIST'])) {
			return;
		}
		
		$cartCollectionMessage = new CardCollectionMessage($commandDetail['senderId']);
		
		//For all card
		$length = count($trendingArticleList['LIST']);
		for($index = 0; $index < $length; $index ++) {
			$trendingArticle = $trendingArticleList['LIST'][$index];
			
			$cardMessage = new CardMessage($trendingArticle['title'], $trendingArticle['description'], $trendingArticle['image']);
			$cardMessage->addWebButton('View Article', $trendingArticle['url']);
			$cardMessage->addPostBackButton('Post Article', json_encode(array('operation'=>self::POSTBACK_POSTREACH_POST_ARTICLE, 'data'=>array('trendingArticleId'=>$trendingArticle['trendingArticleId']))));
			
			if(!empty($trendingArticleList['PAGING']['NEXT_PAGE'])) {
				$payload = array('operation'=>self::POSTBACK_POSTREACH_DEFAULT_STORIES, 'data'=>array('currentPage'=>$trendingArticleList['PAGING']['NEXT_PAGE']));
				
				if(!empty($options['search'])) {
					$payload['data']['search'] = $options['search'];
				}
				
				$cardMessage->addPostBackButton('Next Page', json_encode($payload));
			}
			
			$cartCollectionMessage->addCard($cardMessage);
			
			if($index > 8) {
				break;
			}
		}
		
		$bot->send($cartCollectionMessage->getData());
	}
	
	/**
	 * This function send the new user detection message
	 * @param FbBotApp $bot
	 * @param array $commandDetail
	 */
	public static function sendInputMessage(FbBotApp $bot, $commandDetail, $options = array()) {
		$message = 'Please enter search query to search trending articles, e.g politics';

		$message = new Message($commandDetail['senderId'], self::getMessage($commandDetail, $message));
		$bot->send($message);
	}
	
	/**
	 * This function send the new user detection message
	 * @param FbBotApp $bot
	 * @param array $commandDetail
	 */
	public static function sendPostArticleThank(FbBotApp $bot, $commandDetail, $options = array()) {
		$message = 'Article posted on your social wall successfuly';

		$message = new Message($commandDetail['senderId'], self::getMessage($commandDetail, $message));
		$bot->send($message);
	}
	
	/**
	 * This function send the new user success message
	 * @param FbBotApp $bot
	 * @param array $commandDetail
	 */
	public static function sendSignupMessage(FbBotApp $bot, $commandDetail, $options = array()) {
		$message = 'Your account is created successfully';

		$message = new Message($commandDetail['senderId'], self::getMessage($commandDetail, $message));
		$bot->send($message);
	}
	
	/**
	 * This function send the confirmation link
	 * @param FbBotApp $bot
	 * @param array $commandDetail
	 */
	public static function sendConfirmationLink(FbBotApp $bot, $commandDetail, $accountDetail = array()) {
		/* $message = 'To finish signup process, Please confirm your account.';
		
		$buttonMessage = new ButtonMessage($commandDetail['senderId'], $message);
		$buttonMessage->addWebButton('Confirm', Config::getSParameter('SERVER_APP_PATH') . '/confirm/' . $accountDetail['uniqueKey']);
			
		$bot->send($buttonMessage->getData()); */
		
		$accountLinkingElement = new AccountLinkingElement(Config::getSParameter('SERVER_APP_PATH') . '/confirm/' . $accountDetail['uniqueKey'], 'To finish signup process, Please confirm your account by Login');
		
		$structuredMessage = new StructuredMessage($commandDetail['senderId'], StructuredMessage::TYPE_GENERIC, array('elements'=>array($accountLinkingElement)));
		
		$bot->send($structuredMessage);
	}
	
	/**
	 * This function send the new user fail message
	 * @param FbBotApp $bot
	 * @param array $commandDetail
	 */
	public static function sendSignupFailMessage(FbBotApp $bot, $commandDetail, $options = array()) {
		$message = 'Problem while creating your account, Please try after some time';

		$message = new Message($commandDetail['senderId'], self::getMessage($commandDetail, $message));
		$bot->send($message);
	}
	
	/**
	 * This function send message
	 * @param FbBotApp $bot
	 * @param array $commandDetail
	 * @param string $message
	 */
	public static function sendMessage(FbBotApp $bot, $commandDetail, $message) {
		$message = new Message($commandDetail['senderId'], self::getMessage($commandDetail, $message));
		$bot->send($message);
	}
}