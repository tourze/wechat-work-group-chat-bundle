<?php

declare(strict_types=1);

namespace WechatWorkGroupChatBundle\Controller\Admin;

use Doctrine\ORM\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use WechatWorkGroupChatBundle\Entity\GroupChat;
use WechatWorkGroupChatBundle\Enum\GroupChatStatus;

/**
 * @phpstan-extends AbstractCrudController<GroupChat>
 */
#[AdminCrud(routePath: '/wechat-work-group-chat/group-chat', routeName: 'wechat_work_group_chat_group_chat')]
final class GroupChatCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GroupChat::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('客户群')
            ->setEntityLabelInPlural('客户群')
            ->setSearchFields(['chatId', 'name', 'notice'])
            ->setDefaultSort(['createTime' => 'DESC'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('chatId')
            ->add('name')
            ->add('status')
            ->add('corp')
            ->add('agent')
            ->add('owner')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield TextField::new('chatId', '客户群ID')
            ->setHelp('企业微信客户群的唯一标识符')
            ->setRequired(true)
        ;

        yield TextField::new('name', '群名称')
            ->setHelp('客户群的显示名称')
        ;

        yield ChoiceField::new('status', '跟进状态')
            ->setChoices(array_flip(array_map(
                static fn (GroupChatStatus $status): string => $status->getLabel(),
                GroupChatStatus::cases()
            )))
            ->setHelp('客户群的跟进状态')
        ;

        yield TextareaField::new('notice', '群公告')
            ->setHelp('客户群的公告内容')
            ->onlyOnForms()
        ;

        yield AssociationField::new('corp', '企业')
            ->setHelp('所属企业')
        ;

        yield AssociationField::new('agent', '应用')
            ->setHelp('所属应用')
        ;

        yield AssociationField::new('owner', '群主')
            ->setHelp('客户群的群主')
        ;

        yield AssociationField::new('admins', '管理员')
            ->setHelp('客户群的管理员列表')
            ->onlyOnDetail()
        ;

        yield AssociationField::new('members', '成员')
            ->setHelp('客户群的成员列表（仅显示前10个成员，完整列表请查看成员管理页面）')
            ->onlyOnDetail()
            ->setQueryBuilder(function (EntityRepository $repository) {
                return $repository->createQueryBuilder('m')
                    ->setMaxResults(10)
                    ->orderBy('m.createTime', 'DESC')
                ;
            })
        ;
    }
}
