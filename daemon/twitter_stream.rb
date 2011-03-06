require 'yaml'
require 'tweetstream'
require 'json'
require 'time'
require 'oauth'
require 'json'
consumer = OAuth::Consumer.new('faf4014c58fe2e711b4056e7be22982304d734edb', '1fb63e7a469882feabaaf680b134a4f5', {:site => 'http://nerdout.me'})
access_token = OAuth::AccessToken.new(consumer, '8c9ffa6ce69e2b907861f37bf0b9dfba04d734edb', '602b0c0c3795c50cb59fd189d8ffe55e')
p access_token

config = YAML.load_file('config.yaml')
TweetStream::Client.new(config['username'],config['password']).track('nerdout') do |status|
 #p status.keys
 screen_name = status[:user][:screen_name]
 image_url = status[:user][:profile_image_url]
 begin
	 place_name = status[:place][:name]
rescue
	place_name = nil
end
 content_id = status[:id]
 ruby_time = Time.parse(status[:created_at])
 mysql_time = ruby_time.strftime("%Y-%m-%d %H:%M:%S")
begin 
 place_lat = status[:place][:bounding_box][:coordindates].first.first
 place_long = status[:place][:bounding_box][:coordindates].first[1]
 place_address = status[:place][:attributes][:street_address]
rescue
	place_lat = nil
	place_long = nil
	place_address = nil
end
 #coordinates = status[:coordinates]
 location = status[:user][:location]
 user_id = status[:user][:id]
 
 user_hash = {
	 :source => 'daemon', 
	 :module => 'twitter', 
	 :username => screen_name, 
	 :location_name =>place_name, 
	 :content => status.text, 
	 :address => place_address, 
	 :content_id => content_id,  
	 :timestamp => mysql_time, 
	 :image => image_url, 
	 :url => "http://www.twitter.com/#{screen_name}",
	 :location => location,
	 :remote_user_id => user_id
}
 p user_hash.to_json
  #puts "[#{status.user.screen_name}] #{status.text}"
  p access_token.post("/api/nerdout/create_checkin", user_hash.to_json,{'Content-Type'=>'application/json'})
end
