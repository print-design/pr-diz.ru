%============================
%Раскрой роля. 21.06.22 максимум 13 типоразмеров по ширине
%Минимизация отходов раскроя.
%============================
%Очистка памяти от предыдущих вычмслений
clear;

%---Вход-------------------------------------------------------------------
%Ширина джамбо в мм (исходный ролик под раскрой)
%Wdjmm=1000;
%Wdjmm=1070;
%Wdjmm=1530;
Wdjmm=1500;%405;%1500;

%ширина в метрах
Wdj=Wdjmm/1000;

%Длина джамбо в метрах
Ldj=42000;

%Один съем, метров
Lsm=2000;

%---Выход------------------------------------------------------------------
%За RezNmax (задается ниже) съемов по Lsm метров получить
%роли со следующими параметрами:
%Ширина роликов, мм (максимум 13 значений)
% %Test1
% Wz=[120 140 150 170 185 200 215 220 240 245 255 260 320];
% %Длина роликов соответственно ширине Wz, метров
% Lz=[8000 8000 4000 8000 10000 4000 4000 40000 6000 6000 6000 16000 34000];

%Work
%Wz=[320 120 150 255 240 245 220 200 230 185 140 260];
%Wz=[180 170 120 160 260 240 205 320];
Wz=[120 140 150 260 200 205];

%Длина роликов соответственно ширине Wz, метров
%Lz=[30000 8000 4000 6000 6000 6000 10000 4000 2000 10000 8000 16000];
%Lz=[12000 2000 6000 2000 8000 6000 4000 16000];
Lz=[10000 2000 18000 4000 2000 4000];

%сортировка от минимальной до максимальной ширины
[Wzsort,index]=sort(Wz)

WrNmm=zeros(1,13);
LrN=zeros(1,13);

[ninp,ninp2]=size(Wz);
for k=1:ninp2,
    WrNmm(k)=Wz(index(k));
    LrN(k)=Lz(index(k));
end;    
WrN=WrNmm/1000;

%==========================================================================
%максимальное число резов (съемов с джамбо роля)
RezNmax=3;

%==========================================================================
% минимальное число резов по каждой ширине
n1min0=0;
n2min0=0;
n3min0=0;
n4min0=0;
n5min0=0;
n6min0=0;
n7min0=0;
n8min0=0;
n9min0=0;
n10min0=0;
n11min0=0;
n12min0=0;
n13min0=0;

%==========================================================================
%максимальное число резов по каждой ширине
n1max=LrN(1)/Lsm;
n2max=LrN(2)/Lsm;
n3max=LrN(3)/Lsm;
n4max=LrN(4)/Lsm;
n5max=LrN(5)/Lsm;
n6max=LrN(6)/Lsm;
n7max=LrN(7)/Lsm;
n8max=LrN(8)/Lsm;
n9max=LrN(9)/Lsm;
n10max=LrN(10)/Lsm;
n11max=LrN(11)/Lsm;
n12max=LrN(12)/Lsm;
n13max=LrN(13)/Lsm;

%==========================================================================
nNmin0=[n1min0 n2min0 n3min0 n4min0 n5min0 n6min0 n7min0 n8min0 n9min0 ...
       n10min0 n11min0 n12min0 n13min0];

nNmax=[n1max n2max n3max n4max n5max n6max n7max n8max n9max n10max ...
       n11max n12max n13max];
%==========================================================================
Wi=WrN;
Wimm=WrNmm;
Lim=LrN;
%==========================================================================
[nn1,nn2]=size(Lim);
for i=1:nn2,
    OutSsum(i)=0;
    %OutSsum3(i)=0;
end;    
%==========================================================================
nNmin=nNmin0;
nNm=nNmax; 
%==========================================================================
filename=sprintf('param%dmm.txt', Wdjmm);
    %fid = fopen('param.txt', 'wt');
fid = fopen(filename, 'wt');
fprintf(fid, '---------------------------------------------------------------------------------\n');
fprintf(fid, 'Ширина исходного роля %7d мм; один съем %6d метров\n',Wdjmm,Lsm);
fprintf(fid, '---------------------------------------------------------------------------------\n');
fprintf(fid, 'Задание на раскрой материала :\n\n');
 for i=1:nn2,
      if Lim(i)>0, fprintf(fid, 'номер =%2d; ширина =%4d мм; длина =%6d м;\n', i,Wimm(i),Lim(i)); end;
 end;  
 
%расчет по номерам реза
for RezN=1:RezNmax,
   
    nNo=zeros(1,nn2);
    ni =zeros(1,nn2);
    nio =zeros(1,nn2);
     
    Nk=0;
    Nh=0;
    St=0;
    
    for n1=nNmin(1):nNm(1),
     for n2=nNmin(2):nNm(2),
      for n3=nNmin(3):nNm(3),
       for n4=nNmin(4):nNm(4),
        for n5=nNmin(5):nNm(5),
         for n6=nNmin(6):nNm(6),
          for n7=nNmin(7):nNm(7),
           for n8=nNmin(8):nNm(8),
            for n9=nNmin(9):nNm(9),
             for n10=nNmin(10):nNm(10),
              for n11=nNmin(11):nNm(11),
               for n12=nNmin(12):nNm(12),
                for n13=nNmin(13):nNm(13),               
                             Nk=Nk+1;
                             S=0;
                             ni=[n1 n2 n3 n4 n5 n6 n7 n8 n9 n10 n11 n12 n13];
                             for j=1:nn2,
                                 S=S+ni(j)*Wi(j);
                             end;    
                             if (S<=Wdj)&&(S>St),
                               St=S;
                               nio=ni;
                               Nh=Nk;
                             end;%if       
                end;%13
               end;%12
              end;
             end;
            end;
           end;
          end;
         end;
        end;
       end;
      end;
     end;
    end;%1
    
    for j=1:nn2,
        Out(j)=nio(j);
    end;   
    Out2=[Out St]
    Nko=Nk;
    Nho=Nh;
    Ost=Wdj-St;
    OstMM=Ost*1000;
    fprintf(fid, '---------------------------------------------------------------------------------\n');
    fprintf(fid, 'Рез N%2d; Остатки = %.2f мм X %d м\n\n',RezN,OstMM,Lsm);
    for i=1:nn2,
        OutS(i)=Out(i)*Lsm;
        OutSsum(i)=OutSsum(i)+OutS(i);
        if Out(i)>0,
            wm=Wi(i)*1000;
            fprintf(fid, 'номер =%2d;  ширина =%4d мм;  ручьев =%2d;  длина =%6d м;  сумма длин =%6d м\n', i,wm,Out(i),OutS(i),OutSsum(i));
            nNm(i)=nNm(i)-Out(i);
            if nNm(i)<0, nNm(i)=0; end;
        end
    end;
    %--------------------------------------------  
end; %for RezN=1:RezNmax,
delta=OutSsum - Lim;
fprintf(fid, '=================================================================================\n');
fprintf(fid, '\n');
fprintf(fid, 'ИТОГО: ЗАДАНО/ПОЛУЧЕНО/РАЗНОСТЬ :\n\n',OstMM);
 for i=1:nn2,
     if Lim(i)>0, fprintf(fid, 'номер =%2d; ширина =%4d мм; задано =%6d м; получено =%6d м; разн. =%6d м\n', i,Wimm(i),Lim(i),OutSsum(i),delta(i)); end;
 end;    

 %==========================================================
 %Кроим остатки, при условии, что исходное задание выполнено
 OutSsum3=OutSsum;
 if delta==0,
     if (OstMM>=Wz(1))&&(OstMM<Wdjmm),
         Wdjmm3=OstMM
         %ширина в метрах
         Wdj3=Wdjmm3/1000;
         RezNmax3=1;         
         nNmin=nNmin0;
         nNm=nNmax;     
%расчет по номерам реза
for RezN=1:RezNmax3,
   
    nNo=zeros(1,nn2);
    ni =zeros(1,nn2);
    nio =zeros(1,nn2);
     
    Nk=0;
    Nh=0;
    St=0;
    
    for n1=nNmin(1):nNm(1),
     for n2=nNmin(2):nNm(2),
      for n3=nNmin(3):nNm(3),
       for n4=nNmin(4):nNm(4),
        for n5=nNmin(5):nNm(5),
         for n6=nNmin(6):nNm(6),
          for n7=nNmin(7):nNm(7),
           for n8=nNmin(8):nNm(8),
            for n9=nNmin(9):nNm(9),
             for n10=nNmin(10):nNm(10),
              for n11=nNmin(11):nNm(11),
               for n12=nNmin(12):nNm(12),
                for n13=nNmin(13):nNm(13),               
                             Nk=Nk+1;
                             S=0;
                             ni=[n1 n2 n3 n4 n5 n6 n7 n8 n9 n10 n11 n12 n13];
                             for j=1:nn2,
                                 S=S+ni(j)*Wi(j);
                             end;    
                             if (S<=Wdj3)&&(S>St),
                               St=S;
                               nio=ni;
                               Nh=Nk;
                             end;%if       
                end;%13
               end;%12
              end;%11
             end;%10
            end;%9
           end;%8
          end;%7
         end;%6
        end;%5
       end;%4
      end;%3
     end;%2
    end;%1
    
    for j=1:nn2,
        Out3(j)=nio(j);
    end;   
    Out23=[Out3 St]
    Nko=Nk;
    Nho=Nh;
    Ost3=Wdj3-St;
    OstMM3=Ost3*1000;
    fprintf(fid, '\n');
    fprintf(fid, '=================================================================================\n');
    fprintf(fid, 'Кроим остатки шириной %7d мм; один съем %6d метров\n',Wdjmm3,Lsm);
    fprintf(fid, '---------------------------------------------------------------------------------\n');    
    fprintf(fid, 'Добавляем в рез N%2d:\n\n',RezNmax);
    for i=1:nn2,
        OutS3(i)=Out3(i)*Lsm;
        OutSsum3(i)=OutSsum3(i)+OutS3(i);
        if Out3(i)>0,
            wm=Wi(i)*1000;
            fprintf(fid, 'номер =%2d;  ширина =%4d мм;  ручьев =%2d;  длина =%6d м;\n', i,wm,Out3(i),OutS3(i));
            nNm(i)=nNm(i)-Out3(i);
            if nNm(i)<0, nNm(i)=0; end;
        end
    end;
    fprintf(fid, 'Получаем остатки = %.2f мм X %d м\n\n',OstMM3,Lsm);
    %--------------------------------------------  
end; %for RezN=1:RezNmax,         
         %------------------------------------------------------S
         
delta3=OutSsum3 - Lim;
fprintf(fid, '=================================================================================\n');
fprintf(fid, '\n');
fprintf(fid, 'ИТОГО: ЗАДАНО/ПОЛУЧЕНО/РАЗНОСТЬ=ПОЛУЧЕНО-ЗАДАНО :\n\n',OstMM);
 for i=1:nn2,
     if Lim(i)>0, fprintf(fid, 'номер =%2d; ширина =%4d мм; задано =%6d м; получено =%6d м; разн. =%6d м\n', i,Wimm(i),Lim(i),OutSsum3(i),delta3(i)); end;
 end;            
     end;  %if OstMM>=Wz(1)  
 end; % if delta==0,   
fclose(fid);




