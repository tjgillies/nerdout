require 'foursquare2'
require 'yaml'
require 'json'
require 'oauth'
config = YAML.load_file('config.yaml')
consumer = OAuth::Consumer.new(config['nerdout_consumer_token'], config['nerdout_consumer_secret'], {:site => 'http://nerdout.me'})
access_token = OAuth::AccessToken.new(consumer, config['nerdout_access_token'], config['nerdout_access_secret'])
p access_token
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
		:location => {:name => , :address => , :city =>, :state => , :neighborhood => , :country => },
		:type => 'checkin'
	}.to_json
	p access_token.post("/api/nerdout/create_checkin", foo.to_json,{'Content-Type'=>'application/json'})
end