<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Room_goin;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Searchable;

    protected $guard = 'api';

    function getPicAttribute($pic) {
        if (empty($pic)) {
            return asset('defult.jpg');
        }else {
            return asset($pic);
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'country',
        'gender',
        'pic',
        'birth_date',
        'login_by',
        'token',
        'connection_id',
        'user_status',
        'user_image',
        'fcm_token',
        'last_seen'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen' => 'datetime',
        'user_wallets'=> 'array',
        'user_gifts'=> 'array',
        'is_friend'=> 'boolean',
        'is_followed'=> 'boolean',
        'friendship_hold'=> 'boolean',
    ];


    protected $with=[
    ];

    protected function getLastSeenAttribute() {
        return $this->attributes['last_seen']= \Carbon\Carbon
            :: parse( $this->attributes['last_seen'] )
            -> format('Y-m-d H:i:s');
    }
# ##########################################################
    function get_csrf() {
        return csrf_token() ;
    }
# ##########################################################
    function otp() {
        return $this
            ->hasOne(Otp::class)
            ->where(
                [
                    [
                        "created_at" ,
                        '>=',
                        Carbon::now()
                            ->subMinutes(30)
                            ->toDateTimeString()
                    ]
                ]
            )->select('id', 'user_id', 'otp');
    }

    public function createdByRooms () {
        return $this
            ->hasMany(Room::class, 'user_to_room', 'id');
    }

    public function roomGoins()
    {
        return $this
            ->hasMany(Room_goin::class, 'user_id', 'id')
            ->select([
                "room_goins.id",
                "room_goins.user_id",
                "rooms.id AS room_id",
                "rooms.name",
                "rooms.description",
                "rooms.background",
                "rooms.image",
                "rooms.country",
            ])
            ->join("rooms", "rooms.id","=", "room_goins.room_id")
            ;
    }

    public function roomFolloing()
    {
        return $this->hasMany(FollowRoom::class, 'user_id')
        ->select([
            "follow_rooms.id",
            "follow_rooms.user_id",
            "rooms.id AS room_id",
            "rooms.name",
            "rooms.description",
            "rooms.background",
            "rooms.image",
            "rooms.country",
        ])
        ->join("rooms", "rooms.id","=", "follow_rooms.room_id");
    }

    public function user_badge()
    {
        return $this->hasMany(LevelBadge::class, 'level_id', 'level_id')->with('badge');
    }

    public function roomsByInvitation()
    {
        return $this->hasMany(RoomInvitation::class, 'from_user', 'id');
    }

    public function roomsFromInvitation()
    {
        return $this->hasMany(RoomInvitation::class, 'to_user', 'id');
    }

    public function user_level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id')->with('levelRange');
    }

    public function posts () {
        return $this->hasMany(Post::class);
    }

    public function giftsTo () {
        return $this->hasMany(UsersGifts::class, 'from_user_id', 'id')->with(['gift', 'fromUser', 'toUser']);
    }

    public function giftsFrom () {
        return $this->hasMany(UsersGifts::class, 'from_user_id', 'id')->with(['gift', 'fromUser', 'toUser']);
    }

    public function likesPosts () {
        return $this->hasMany(PostsLike::class);
    }

    public function comments () {
        return $this->hasMany(Comment::class);
    }

    public function likesComments () {
        return $this->hasMany(CommentsLike::class);
    }

    public function frindRequestTo () {
        return $this->hasMany(FriendRequest::class, 'to_user');
    }

    public function frindRequestFrom () {
        return $this->hasMany(FriendRequest::class, 'from_user');
    }

    public function friendCurrent () {
        return $this->hasMany(FriendRequest::class, 'from_user', 'id')->where(['from_user'=> Auth::user()->id, 'is_accepted'=>'1'])->with(['fromUser', 'ToUser']);
    }

    public function requestsFromFriends () {
        return $this->hasMany(FriendRequest::class, 'from_user', 'id')->where(['from_user'=> Auth::user()->id, 'is_accepted'=>'0'])->with(['fromUser', 'ToUser']);
    }

    public function requestsToFriends () {
        return $this->hasMany(FriendRequest::class, 'to_user', 'id')->where('to_user', Auth::user()->id)->with(['fromUser', 'ToUser']);
    }

    public function frindFollowTo () {
        return $this
            -> hasMany(FriendFollow::class, 'to_user', 'id')
            -> join('users', 'users.id', '=','friend_follows.to_user')
            -> select([
                'friend_follows.id' ,
                'friend_follows.to_user' ,
                'friend_follows.from_user',
                'friend_follows.is_accepted',
                'users.name',
                'users.id AS user_id',
                'users.pic'
            ])
            ;
    }

    public function frindFollowFrom () {
        return $this
            -> hasMany(FriendFollow::class, 'from_user')
            -> join('users', 'users.id', '=','friend_follows.to_user')
            -> select([
                'friend_follows.id' ,
                'friend_follows.to_user' ,
                'friend_follows.from_user',
                'friend_follows.is_accepted',
                'users.name',
                'users.id AS user_id',
                'users.pic'
            ])
            ;
    }

    public function subCategoriesSubscripes () {
        return $this->hasMany(sub_categories_subscripe::class);
    }
    # ##########################################################


    public function black_list () {
        return $this
            -> hasMany( black_list_user::class, 'to_user' ,'id'  )
            ->select(["users.name", "users.id", "black_list_users.to_user", "users.pic"])
            -> join("users", "users.id","=", "black_list_users.to_user");
    }
    # ##########################################################

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'name'          => $this->name,
            'email'         => $this->email,
            'mobile'        => $this->mobile,
        ];
    }
}
