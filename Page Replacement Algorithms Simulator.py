request = []
cache = []


def fifo():
    # global request
    # global cache
    for i in request:
        if i in cache:
            print("Hit")
        else:
            print("Miss")
            if len(cache) < 8:
                cache.append(i)
            else:
                cache.pop(0)
                cache.append(i)


def lfu():
    # global request
    # global cache
    temporaryrecord = []

    for r in request:
        if r in cache:
            print("Hit")
            temporaryrecord.append(r)
        else:
            if len(cache) < 8:
                print("Miss")
                cache.append(r)
                temporaryrecord.append(r)
            else:
                print("Miss")
                temporaryrecordSet = set(temporaryrecord)
                ft = float('inf')
                for o in temporaryrecordSet:
                    if o in cache:
                        temprec = temporaryrecord.count(o)
                        if temprec < ft:
                            ft = temprec

                minlist = []
                for g in cache:
                    if temporaryrecord.count(g) == ft:
                        minlist.append(g)

                if len(minlist) == 1:
                    cache.remove(minlist[0])
                    cache.append(r)
                    temporaryrecord.append(r)
                else:
                    cache.remove(min(minlist))
                    cache.append(r)
                    temporaryrecord.append(r)

try:
    while True:

        while True:
            number = int(input("Enter the page in memory: "))
            if number == 0:
                break
            else:
                request.append(number)

        choice = input("Enter the option of your choice \nPress 1 for fifo\nPress 2 for lfu \nPress Q tp quit program\n")
        if choice == "1":
            fifo()
            print(cache)
            cache.clear()
            request.clear()
        elif choice == "2":
            lfu()
            print(cache)
            cache.clear()
            request.clear()
        elif choice.upper() == "q":
            # print('z')
            exit()
except:
    exit()
