# coding=utf-8
from django.shortcuts import render_to_response
from django.http import HttpResponse, HttpResponseNotAllowed,\
    HttpResponseNotFound
from django.db.models.aggregates import Sum
import django.utils.simplejson as json
from Scoreboard.utils import *
from Scoreboard.models import *

#Ajax stuff
#Checks for allready sended flag, if not - create one
def send_check_flag(request):
    if not request.is_ajax():
        return HttpResponseNotAllowed('Ajax')

    task_id = request.GET.get('task_id')
    team = get_team(get_ip(request))
    sended_flag = request.GET.get('flag')
    
    task = Task.objects.get(id=task_id)
    result = check_flag(team,task,sended_flag)
    jsonDict = { "status": result }
    return HttpResponse( json.dumps( jsonDict ), mimetype="application/json" )

#Retrieve info for task
def task_info(request):
    if not request.is_ajax():
        return HttpResponseNotAllowed('Ajax')
    task_id = request.GET.get('task_id')
    team = get_team(get_ip(request))
    task = Task.objects.get(id=task_id)
    jsonDict = {'task' : task.description, 'score' : task.score, 'status' : isSolveTask(team,task) }
    return HttpResponse( json.dumps( jsonDict ), mimetype="application/json" )

#Teams with scores
#Returns: json with array of dict: [{'place'=1,'team'=TeamObject(see Models),'total_score'=1000,'category'=[100, 200, 100...]}...]
#Category field contains data about scores from all categories
def scores(request):
    if not request.is_ajax():
        return HttpResponseNotAllowed('Ajax')
    teams = Team.objects.all()
    categories = Category.objects.all()
    scores = Score.objects.select_related()
    data = [{'team' : t,
             'total_score' : int( scores.filter(team=t).aggregate(sum=Sum('task__score'))['sum'] or 0 ),
             'category' : [ int( scores.filter(team=t, task__isnull=False, task__category=c).aggregate(s=Sum('task__score'))['s'] or 0 )
                             for c in categories]
             } for t in teams].sort(key=lambda x: x['total_score'])

    for (i, d) in enumerate(data):
		d['place'] = i+1
    
    return HttpResponse(json.dumps(data), mimetype="application/json")

#Lightweight return of "teamid - totalscore - place" data
#For some runtime dynamic stuff with ajax
def places(request):
    if not request.is_ajax():
        return HttpResponseNotAllowed('Ajax')
    teams = Team.objects.all()
    scores = Score.objects.select_related()
    
    data = [{'id' : t.id,
             'total_score' : int( scores.filter(team=t).aggregate(sum=Sum('task__score'))['sum'] or 0 )
             } for t in teams].sort(key=lambda x: x['total_score'])

    for (i, d) in enumerate(data):
		d['place'] = i+1
  
    return HttpResponse(json.dumps(data), mimetype="application/json")

#Pages
#Scoreboard page
def scoreboard(request):
    client_ip = get_ip(request)
    
    team = get_team(client_ip)
    
    teams = Team.objects.all()
    categories = Category.objects.all()
    scores = Score.objects.select_related()
    data = [
            {'team' : t,
             'total_score' : int( scores.filter(team=t).aggregate(s=Sum('task__score'))['s'] or 0 ),
             'category' : [ int( scores.filter(team=t, task__isnull=False, task__category=c).aggregate(s=Sum('task__score'))['s'] or 0 )
                             for c in categories]

             } for t in teams]
    data.sort(key=lambda x: x['total_score'],reverse=True)
    for (i, d) in enumerate(data):
		d['place'] = i+1
    
    return render_to_response('scoreboard.html',
                              {'team' : team, 
                               'user_address' : client_ip,
                               'data' : data,
                               'categories' : categories
                               },
							   #context_instance=RequestContext(request),
							   mimetype="application/xhtml+xml")

#View team stats
def team(request, team_id):
    client_ip = get_ip(request)
    my_team = get_team(client_ip)
    team = Team.objects.get(id=team_id)
    categories = Category.objects.all()
    teams = Team.objects.all()
    scores = Score.objects.filter(team=team)
    
    access_tasks = my_team is not None and team.id == my_team.id
    
    tasks = Task.objects.filter(visible=True)

    dteams = [{'team' : t} for t in teams]
    
    data = [ {'cat' :cat,
              'tasks' : [{'task' : task, 'issolved': scores['task']} for task in tasks.filter(category=cat)]} for cat in categories]
        
    if team is None:
        return HttpResponseNotFound()

    return render_to_response('team.html',
                              {'team' : team, 
                               'teams' : dteams,
                               'data' : data,
                               'user_address' : get_ip(request),
                               'access' : access_tasks

                               },
							   mimetype="application/xhtml+xml")

#Try to show my team only
def myteam(request):
    client_ip = get_ip(request)
    team = get_team(client_ip)

    return team(request, team.id)
