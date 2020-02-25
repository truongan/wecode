#include <iostream>

using namespace std;

int main()
{
    char can[][10] = {"GIAP", "AT", "BINH", "DINH", "MAU", "KY", "CANH", "TAN", "NHAM", "QUY"};
    char chi[][10] ={"TY'", "SUU", "DAN", "MEO", "THIN", "TY.", "NGO", "MUI", "THAN", "DAU", "TUAT", "HOI"};
    int n;
    cin >> n;

    int can_2015 = 1;
    int chi_2015 = 7;


    if (n < 0) n++;

    int delta =  n - 2015 ;

    cout << can[ ((delta + can_2015) % 10 + 10)%10 ] << " "
        << chi[ ((delta + chi_2015) % 12 + 12)% 12 ];
    return 0;
}
