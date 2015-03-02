select dy.user_id as uid, dy.name as sub_name, dyy.name as super_name, d.id as id, d.date, d.url, d.notes, d.amount, d.sub_type_id, dy.supertypeid 
from sandbox.daily_record d 
left join sandbox.dailysubtypes dy on dy.id = d.sub_type_id 
left join dailysupertypes dyy on dy.supertypeid = dyy.id 
where d.user_id = 3 and (dy.user_id is null or dy.user_id = 3)
union 
select dy.user_id as uid, dy.name as sub_name, dyy.name as super_name, d.id as id, d.date, d.url, d.notes, d.amount, d.sub_type_id, dy.supertypeid 
from sandbox.users u
left join sandbox.users_temp temp on u.temp_user_id = temp.id
left join sandbox.daily_record d on d.user_id = temp.id
left join sandbox.dailysubtypes dy on dy.id = d.sub_type_id 
left join dailysupertypes dyy on dy.supertypeid = dyy.id 
where u.temp_user_id = 3

