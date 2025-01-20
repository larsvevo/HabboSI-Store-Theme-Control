<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\CameraWeb;
use Atom\Core\Models\WebsiteArticle;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Atom\Core\Models\Room;
use Atom\Core\Models\Guild;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Handle an incoming request.
     */
    public function __invoke(Request $request): View
    {

        $currentUserId = auth()->id();
        
        $articles = WebsiteArticle::with('user')
            ->where('is_published', true)
            ->latest('id')
            ->skip(1)
            ->take(4)
            ->get();

        $article = WebsiteArticle::with('user')
            ->where('is_published', true)
            ->latest('id')
            ->first();


        $friends = $request->user()
        ->friends()
        ->whereRelation('friend', 'online', '1')
        ->get();


        $photos = CameraWeb::latest('id')
        ->take(4)
        ->with('user:id,username,look')
        ->get();

        $lastOnlineTimestamp = DB::table('users')
        ->where('id', $currentUserId)
        ->value('last_online');

        $lastOnline = Carbon::createFromTimestamp($lastOnlineTimestamp)
            ->locale('de') 
            ->diffForHumans();


            
        $topRooms = Room::orderByDesc('users')
            ->select('name', 'users', 'users_max', 'owner_name')
            ->limit(3)
            ->get();

        $topGuilds = Guild::join('guilds_members', 'guilds.id', '=', 'guilds_members.guild_id')
            ->select('guilds.name', DB::raw('COUNT(guilds_members.id) as member_count'))
            ->groupBy('guilds.id', 'guilds.name')
            ->orderByDesc('member_count')
            ->limit(3)
            ->get(); 



        $relationships = [];

        foreach ([1, 2, 3] as $relation) {
            $user = DB::table('messenger_friendships as m1')
                ->join('messenger_friendships as m2', 'm1.user_one_id', '=', 'm2.user_one_id')
                ->join('users as u', 'u.id', '=', 'm2.user_two_id')
                ->select(
                    'm1.user_one_id as current_user_id',
                    'm2.user_two_id as friend_id',
                    'm2.relation',
                    'u.username as friend_name',
                    'u.look as friend_avatar'
                )
                ->where('m1.user_one_id', $currentUserId)
                ->where('m2.relation', $relation)
                ->inRandomOrder()  
                ->limit(1) 
                ->first();

            if ($user) {
                $relationships[] = $user;  
            }
        }
    


        return view('home', compact('articles', 'article','topRooms', 'friends', 'photos', 'topGuilds', 'relationships', 'lastOnline'));
    }
}
