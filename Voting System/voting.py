from openpyxl import workbook

def generatePreferences(values):
    table = []
    for row in values.iter_rows():
        row_data = []
        for cell in row:
            row_data.append(cell.value)
        table.append(row_data)

    preferenceProfileOfAgents = {}
    agents = 1
    for i in table:

        valuesadded = []
        preferencelist = []

        for j in enumerate(i, 1):
            valuesadded.append(j)
            print((valuesadded))

        valuesadded.sort(key=lambda valuesadded: (valuesadded[1],valuesadded[0]), reverse=True)
        print(valuesadded)

        for k in valuesadded:
            preferencelist.append(k[0])

        print(preferencelist)
        preferenceProfileOfAgents[agents] = preferencelist
        agents += 1

    return preferenceProfileOfAgents

def dictatorship(preferenceProfile, agent):

    if agent > len(preferenceProfile) or agent == 0:
        raise Exception('Not the correct value of agent')
    else:
        selectedAlternative = preferenceProfile[agent][0]
    return selectedAlternative

def scoringRule(preferences, scoreVector, tieBreak):

    if len(scoreVector) != len(preferences[1]):
        print("Incorrect input")
        return False

    indexNumberofAgent ={}

    scoreVector.sort(reverse=True)

    for p in preferences:
        scoreVectorindices = 0
        for q in preferences[p]:
            if q not in indexNumberofAgent:
                indexNumberofAgent[q] = scoreVector[scoreVectorindices]
            else:
                indexNumberofAgent[q] += scoreVector[scoreVectorindices]

            scoreVectorindices+=1
    temporary= max(indexNumberofAgent.values())
    winners=[k for k in indexNumberofAgent if indexNumberofAgent[k] == temporary]

    if len(winners)== 1:
        return winners[0]

    if tieBreak == "max":
        return max(winners)
    elif tieBreak =="min":
        return min(winners)
    for t in preferences[tieBreak]:
        if t in winners:
            return t

def plurality(preferences, tieBreak):

    indexNumberofAgent={}
    for q in preferences:
        if preferences[q][0] not in indexNumberofAgent:
            indexNumberofAgent[preferences[q][0]] = 1
        else:
            indexNumberofAgent[preferences[q][0]] += 1

    temporary = max(indexNumberofAgent.values())
    winners = [k for k in indexNumberofAgent if indexNumberofAgent[k] == temporary]
    if len(winners) == 1:
        return winners[0]

    if tieBreak == "max":
        return max(winners)
    elif tieBreak == "min":
        return min(winners)
    for t in preferences[tieBreak]:
        if t in winners:
            return t

def veto(preferences, tieBreak):
    indexNumberofAgent ={}

    for q in preferences:
        for r in preferences[q]:
            if r != preferences[q][-1]:
                if r not in indexNumberofAgent:
                    indexNumberofAgent[r]=1
                else:
                    indexNumberofAgent[r]+=1
            else:
                if r not in indexNumberofAgent:
                    indexNumberofAgent[r]=0
    temporary = max(indexNumberofAgent.values())
    winners = [k for k in indexNumberofAgent if indexNumberofAgent[k] == temporary]
    if len(winners)== 1:
        return winners[0]

    if tieBreak == "max":
        return max(winners)
    elif tieBreak == "min":
        return min(winners)
    for t in preferences[tieBreak]:
        if t in winners:
            return t

def borda(preferences, tieBreak):
    indexNumberofAgent={}
    for q in preferences:
        m=len(preferences[q])-1
        for r in preferences[q]:
            if r not in indexNumberofAgent:
                indexNumberofAgent[r]=m
            else:
                indexNumberofAgent[r]+=m
            m-= 1
    temporary = max(indexNumberofAgent.values())
    winners = [k for k in indexNumberofAgent if indexNumberofAgent[k] == temporary]
    if len(winners)== 1:
        return winners[0]

    if tieBreak == "max":
        return max(winners)
    elif tieBreak == "min":
        return min(winners)
    for t in preferences[tieBreak]:
        if t in winners:
            return t

def harmonic(preferences, tieBreak):

    indexNumberofAgent={}
    for q in preferences:
        m=1
        for r in preferences[q]:
            if r not in indexNumberofAgent:
                indexNumberofAgent[r]=(1/m)
            else:
                indexNumberofAgent[r]+=(1/m)
            m+=1
    temporary = max(indexNumberofAgent.values())
    winners = [k for k in indexNumberofAgent if indexNumberofAgent[k] == temporary]
    if len(winners)== 1:
        return winners[0]

    if tieBreak == "max":
        return max(winners)
    elif tieBreak == "min":
        return min(winners)
    for t in preferences[tieBreak]:
        if t in winners:
            return t

def rangeVoting(values, tieBreak):
    table = []
    for row in values.iter_rows():
        row_data = []
        for cell in row:
            row_data.append(cell.value)
        table.append(row_data)
    indexNumberofAgent={}
    for q in table:
        m=1
        for r in q:
            if m not in indexNumberofAgent:
                indexNumberofAgent[m]= r
            else:
                indexNumberofAgent[m]+=r
            m+=1
    temporary = max(indexNumberofAgent.values())
    winners = [k for k in indexNumberofAgent if indexNumberofAgent[k] == temporary]
    if len(winners)== 1:
        return winners[0]
    listofalternatives = generatePreferences(values)
    if tieBreak == "max":
        return max(winners)
    elif tieBreak == "min":
        return min(winners)
    for t in listofalternatives[tieBreak]:
        if t in winners:
            return t








