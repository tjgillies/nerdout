require 'yaml'
require 'tweetstream'
require 'json'
require 'time'
require 'oauth'

config			= YAML.load_file('config.yaml')
consumer 		= OAuth::Consumer.new(config['nerdout_consumer_token'], config['nerdout_consumer_secret'], {:site => config['api_endpoint']})
access_token 	= OAuth::AccessToken.new(consumer, config['nerdout_access_token'], config['nerdout_access_secret'])

p access_token

TweetStream::Client.new(config['username'],config['password']).track('nerdout') do |status|
 #p status.keys
 screen_name	= status[:user][:screen_name]
 image_url		= status[:user][:profile_image_url]
 begin
	 place_name = status[:place][:name]
rescue
	place_name	= nil
end
	place_country = status[:place][:country_code]
	
 content_id		= status[:id]
 ruby_time		= Time.parse(status[:created_at])
 mysql_time		= ruby_time.strftime("%Y-%m-%d %H:%M:%S")
begin 
 place_lat 		= status[:place][:bounding_box][:coordindates].first.first
 place_long 	= status[:place][:bounding_box][:coordindates].first[1]
 place_address 	= status[:place][:attributes][:street_address]
rescue
	place_lat 		= nil
	place_long 		= nil
	place_address	= nil
end
begin 
	user_url = status[:user][:url]
rescue
	user_url = "http://www.twitter.com/#{screen_name}"
end
 #coordinates 	= status[:coordinates]
 location		= status[:user][:location]
 user_id 		= status[:user][:id]
 if status[:place][:place_type] == "city"
 	place_city = [:place][:name]
 end
 if status[:place][:place_type] == "neighborhood"
 	place_neighborhood = [:place][:name]
 end
 user_hash = {
	 :source 			=> 'daemon', 
	 :module 			=> 'twitter', 
	 :type 				=> 'tweet',
	 :username 			=> screen_name, 
	 :location_name 	=> place_name, 
	 :content 			=> status.text, 
	 :address 			=> place_address, 
	 :content_id 		=> content_id,  
	 :timestamp 		=> mysql_time, 
	 :image 			=> image_url, 
	 :geo_lat			=> place_lat,
	 :geo_long			=> place_long,
	 :url 				=> user_url,
	 :user_location 	=> location,
	 :location			=> { :name => place_name, :address => place_address, :city => place_city, :state => place_state, :neighborhood => place_neighborhood, :country => place_country }
	 :remote_user_id 	=> user_id
	 
}
 p user_hash.to_json
  #puts "[#{status.user.screen_name}] #{status.text}"
  p access_token.post("/api/nerdout/create_checkin", user_hash.to_json,{'Content-Type'=>'application/json'})
end
