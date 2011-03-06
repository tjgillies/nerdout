require 'foursquare2'
require 'yaml'
require 'json'
require 'oauth'
consumer = OAuth::Consumer.new('faf4014c58fe2e711b4056e7be22982304d734edb', '1fb63e7a469882feabaaf680b134a4f5', {:site => 'http://nerdout.me'})
access_token = OAuth::AccessToken.new(consumer, '8c9ffa6ce69e2b907861f37bf0b9dfba04d734edb', '602b0c0c3795c50cb59fd189d8ffe55e')
p access_token
config =YAML::load_file('config.yaml')
p config['oauth_token']
client = Foursquare2::Client.new(:oauth_token => config['oauth_token'])
#friends = client.user_friends('self').items
client.recent_checkins.each do |user|
	next if not user.shout?
	next if not user.shout =~ /#/
	firstname = user.user.firstName
	lastname = user.user.lastName
	shout = user.shout
	image = user.user.photo
	username = user.id
	begin
		location_name = user.venue.name
		address = user.venue.location.address
	rescue
		location_name = nil
		address = nil
	end
	#p user.venue
	name = "#{firstname} #{lastname}"
	
	location = user.homeCity
	#p user
	p foo = { 
		:source => "daemon",
		:module => "foursquare",
		:username => username,
		:location_name => location_name,
		:name => name,
		:content => shout,
		:address => address,
		:image => image,
		:location => location
	}.to_json
	p access_token.post("/api/nerdout/create_checkin", foo.to_json,{'Content-Type'=>'application/json'})
end