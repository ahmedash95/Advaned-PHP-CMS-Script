// users table
   | id
   | user_group_id
   | username
   | password
   | email
   | status
   | code
   | join_date
   | last_login
// user_details table
   | id
   | user_id
   | fullname
   | image
   | signature
   | mobile
   | gender
   | birthdate
   | country_id
   | city_id
   | zone_id
   | language_id
   |
// user_groups table
   | id
   | name
   | permissions // => collection of permissions ids
// permissions table
   | id
   | script
   | page
   | permissions
// posts table
   | id
   | categories
   | status
   | author_id  => user_id
   | date
   | rating
   | likes
   | dislikes
   | views
   | last_edit_date
   | last_edit_by
   |
// posts_details table
   | id
   | language_id
   | post_id
   | title
   | description
   | image
   | tags
   | custom_url // http://sitename.com/posts/25/6/2015/some-custom-words
// categories table
   | id
   | parent_id // category_id
   | moderators
   | status
// categories_details table
   | id
   | language_id
   | category_id
   | name
   | description
   | image
   | tags
   | custom_url
// modules table
   | id
   | name
   | value
   | status
// comments table
   | id
   | parent_id
   | post_id
   | user_id
   | date
   | last_edit_date
   | last_edit_by
// settings table
   | id
   | group
   | key
   | value
// languages table
   | id
   | name
   | code
   | flag
   | direction   rtl - ltr
   | status     1/0
//