"""

The code defines two classes: BasicAccount and PremiumAccount, which are used to simulate bank accounts. 
The BasicAccount class has methods for depositing and withdrawing money, getting the available balance, printing the balance, getting the account holder's name and account number, issuing a new card, and closing the account. 
The PremiumAccount class is a subclass of BasicAccount and has additional functionality for setting an overdraft limit, getting the available balance (including the overdraft limit), and printing the balance (which shows the overdraft limit if it's being used).
The code creates an instance of the BasicAccount class named User1, with an opening balance of £400.00 and the account holder's name "Alex". 
The User1 account then withdraws £50.00 using the withdraw() method, which updates the balance and prints a message to indicate the new balance.

"""

import random
import datetime
random.seed(5)

class BasicAccount():

    BasicAccountno = 1

    def __init__(self, acName:str,openingBalance:float):
        self.name = acName
        self.acNum = int(BasicAccount.BasicAccountno)
        self.balance = openingBalance
        self.cardNum = random.randint(1000000000000000, 9999999999999999)

        BasicAccount.BasicAccountno +=1

    def __str__(self):
        return f"Name: {self.name}  opening Balance: £{self.balance}"


    def deposit(self, amount:float):
        if amount <= 0:
            print("You can only deposit a sum larger than 0")
        else:
            self.balance = self.balance + amount
            print("Account balance has been updated : £", self.balance)

    def withdraw(self, amount:float):
        if amount <= 0:
            return
        else:
            if amount > self.balance:
                print(f"Can not withdraw £{amount}")
            else:
                self.balance = self.balance - amount
                print(f"{self.name} has withdrawn £{amount}. New balance is £{self.balance}")

    def getAvailableBalance(self):
        return (self.balance)

    def printBalance(self):
        print(f"Current balance: £{self.balance}")

    def getBalance(self):
        return (self.balance)

    def getName(self):
        return(self.name)

    def getAcNum(self):
        return(str(self.acNum))

    def issueNewCard(self):
        today = (datetime.datetime.now() + datetime.timedelta(days=3*365)).strftime('%m,%-y')
        month = int(today[0:2])
        year = int(today[3:5])
        self.cardExp = (month,year)
        self.cardNum = cardnum = random.randint(1000000000000000, 9999999999999999)

        print(self.cardExp)

    def closeAccount(self):
        amount = self.balance
        self.withdraw(amount)
        return True


class PremiumAccount(BasicAccount):

    def __init__(self, acName, openingBalance, initialoverdraft):
        super().__init__(acName, openingBalance)
        self.overdraftLimit = initialoverdraft
        self.overdraft = True

    def __str__(self):
        return f"Name: {self.name}  opening Balance: £{self.balance} initialOverdraft: {self.overdraftLimit}"

    def setOverdraftLimit(self, newLimit):
        self.overdraftLimit = newLimit

    def getAvailableBalance(self):
        return (self.balance + self.overdraftLimit)

    def printBalance(self):
        if self.balance >= 0:
            print(f"Current balance: £{self.balance}\n Overdraft: £{self.overdraftLimit}")
        else:
            print(f"Current balance: £{self.balance}\n RemainingOverdraft: £{self.overdraftLimit + self.balance}")

    def getBalance(self):
            return self.balance

    def withdraw(self, amount:float):
        if amount <= 0:
            return
        else:
            if amount > self.balance + self.overdraftLimit:
                print(f"Can not withdraw £{amount})")
            else:
                self.balance = self.balance - amount
                print(f"{self.name} has withdrawn £{amount}. New balance is £{self.balance}")

    def closeAccount(self):
        if self.balance < 0:
            print(f"Cannot close account due to customer being overdrawn by £{- self.balance}")
            return False
        else:
            amount = self.balance
            self.withdraw(amount)
            return True


User1 = BasicAccount("Alex", 400.00)

User1.withdraw(50.00)



































