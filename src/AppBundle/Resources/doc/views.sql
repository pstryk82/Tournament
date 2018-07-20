-- legacy ---------------------------------
create or replace view league_standings as 
    select t.name, p.* from participant as p
        join abstract_participant as ap on ap.id = p.id
        join team as t on ap.team_id = t.id
    order by
        p.points desc,
        p.goal_difference desc,
        p.goals_for desc;

create or replace view games_pretty as
select thome.name as home, taway.name as away, g.home_team_score as H, g.away_team_score as A, twinner.name from game as g
	join participant as phome on g.home_participant_id = phome.id
	join participant as paway on g.away_participant_id = paway.id
	join team as thome on phome.id = thome.id
	join team as taway on paway.id = taway.id
    left join team as twinner on g.winner_id = twinner.id
;


-- read model ----------------------------
select
    ap.id, t.name, lp.games_played,
    lp.points, lp.goals_for, lp.goals_against, lp.goal_difference
from abstract_participant as ap
join league_participant as lp on ap.id = lp.id
join team as t on ap.team_id = t.id
join competition c on ap.competition_id = c.id
where c.name = "Top Clubs' League"
order by points desc, goal_difference desc, goals_for desc;

select thome.name as home, taway.name as away, g.home_score as H, g.away_score as A from game as g
	join abstract_participant as phome on g.home_participant_id = phome.id
	join abstract_participant as paway on g.away_participant_id = paway.id
	join team as thome on phome.team_id = thome.id
	join team as taway on paway.team_id = taway.id
;
